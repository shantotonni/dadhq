<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Student List']"/>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex">
                <div class="flex-grow-1">
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <select name="sessionId" id="sessionId" v-model="sessionId" class="form-control">
                          <option disabled value="">Select Session</option>
                          <option :value="session.session_id" v-for="(session , index) in sessions" :key="index">{{ session.name }}</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <input v-model="roll_number" type="text" class="form-control" placeholder="Roll Number">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" @click="getAllStudent" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-12">
          <div class="card">
            <div class="datatable" v-if="!isLoading">
              <div class="card-body">
                <div class="d-flex">
                  <div class="flex-grow-1">
                    <div class="row">
                      <div class="col-md-2">
<!--                        <input v-model="query" type="text" class="form-control" placeholder="Search">-->
                      </div>
                    </div>
                  </div>
                  <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" @click="createStudentModel">
                      <i class="fas fa-plus"></i>
                      Add Student
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" @click="reload">
                      <i class="fas fa-sync"></i>
                      Reload
                    </button>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead>
                      <tr>
                        <th>SN</th>
                        <th>Session name</th>
                        <th>Student Name</th>
                        <th>Student Type</th>
                        <th>Roll</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Date Of Birth</th>
                        <th>Nid</th>
                        <th>Address</th>
                        <th>Nationality</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(student, i) in students" :key="student.student_id" v-if="students.length">
                        <th scope="row">{{ ++i }}</th>
                        <td>{{ student.session_name }}</td>
                        <td>{{ student.first_name }} {{ student.last_name }}</td>
                        <td>{{ student.category_name }}</td>
                        <td>{{ student.roll_no }}</td>
                        <td>{{ student.email }}</td>
                        <td>{{ student.mobile }}</td>
                        <td>{{ student.date_of_birth }}</td>
                        <td>{{ student.nid }}</td>
                        <td>{{ student.address }}</td>
                        <td>{{ student.nationality }}</td>
                        <td>{{ student.status }}</td>
                        <td>
                          <button @click="edit(student)" class="btn btn-success btn-sm"><i class="far fa-edit"></i></button>
                          <!--                                                    <button @click="destroy(service_category.id)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-4">
                    <div class="data-count">
                      Show {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} rows
                    </div>
                  </div>
                  <div class="col-8">
                    <pagination
                        v-if="pagination.last_page > 1"
                        :pagination="pagination"
                        :offset="5"
                        @paginate="query === '' ? getAllStudent() : searchData()"
                    ></pagination>
                  </div>
                </div>
              </div>
            </div>
            <div v-else>
              <skeleton-loader :row="14"/>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--  Modal content for the above example -->
    <div class="modal fade" id="StudentModelModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title mt-0" id="myLargeModalLabel">{{ editMode ? "Edit" : "Add" }} Student</h5>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" @click="closeModal">×</button>
          </div>
          <form @submit.prevent="editMode ? update() : store()" @keydown="form.onKeydown($event)">
            <div class="modal-body">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Select Session</label>
                      <select name="session_id" id="session_id" class="form-control" v-model="form.session_id" :class="{ 'is-invalid': form.errors.has('session_id') }" @change="LoadScheduleData()">
                        <option disabled value="">Select Session</option>
                        <option :value="session.session_id" v-for="(session , index) in sessions" :key="index">{{ session.name }}</option>
                      </select>
                      <div class="error" v-if="form.errors.has('session_id')" v-html="form.errors.get('session_id')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Select Category</label>
                      <select name="category_id" id="category_id" class="form-control" v-model="form.category_id" :class="{ 'is-invalid': form.errors.has('category_id') }" @change="LoadScheduleData()">
                        <option disabled value="">Select Category</option>
                        <option :value="category.id" v-for="(category , index) in categories" :key="index">{{ category.name }}</option>
                      </select>
                      <div class="error" v-if="form.errors.has('category_id')" v-html="form.errors.get('category_id')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>First Name</label>
                      <input type="text" name="first_name" v-model="form.first_name" class="form-control" :class="{ 'is-invalid': form.errors.has('first_name') }">
                      <div class="error" v-if="form.errors.has('first_name')" v-html="form.errors.get('first_name')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Last Name</label>
                      <input type="text" name="last_name" v-model="form.last_name" class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }">
                      <div class="error" v-if="form.errors.has('last_name')" v-html="form.errors.get('last_name')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Roll No</label>
                      <input type="text" name="roll_no" v-model="form.roll_no" class="form-control" :class="{ 'is-invalid': form.errors.has('roll_no') }">
                      <div class="error" v-if="form.errors.has('roll_no')" v-html="form.errors.get('roll_no')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Batch Number</label>
                      <input type="text" name="batch_number" v-model="form.batch_number" class="form-control" readonly :class="{ 'is-invalid': form.errors.has('batch_number') }">
                      <div class="error" v-if="form.errors.has('batch_number')" v-html="form.errors.get('batch_number')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Email</label>
                      <input type="text" name="email" v-model="form.email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                      <div class="error" v-if="form.errors.has('email')" v-html="form.errors.get('email')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Mobile</label>
                      <input type="text" name="mobile" v-model="form.mobile" class="form-control" :class="{ 'is-invalid': form.errors.has('mobile') }">
                      <div class="error" v-if="form.errors.has('mobile')" v-html="form.errors.get('mobile')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Date Of Birth</label>
                      <datepicker v-model="form.date_of_birth" :format="customFormatter" placeholder="Enter Date Of Birth" input-class="form-control"></datepicker>
<!--                      <input type="text" name="date_of_birth" v-model="form.date_of_birth" class="form-control" :class="{ 'is-invalid': form.errors.has('date_of_birth') }">-->
                      <div class="error" v-if="form.errors.has('date_of_birth')" v-html="form.errors.get('date_of_birth')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>NID</label>
                      <input type="text" name="nid" v-model="form.nid" class="form-control" :class="{ 'is-invalid': form.errors.has('nid') }">
                      <div class="error" v-if="form.errors.has('nid')" v-html="form.errors.get('nid')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Address</label>
                      <input type="text" name="address" v-model="form.address" class="form-control" :class="{ 'is-invalid': form.errors.has('address') }">
                      <div class="error" v-if="form.errors.has('address')" v-html="form.errors.get('address')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nationality</label>
                      <input type="text" name="nationality" v-model="form.nationality" class="form-control" :class="{ 'is-invalid': form.errors.has('nationality') }">
                      <div class="error" v-if="form.errors.has('nationality')" v-html="form.errors.get('nationality')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Hostel</label>
                      <select name="is_hostel" id="is_hostel" class="form-control" v-model="form.is_hostel" :class="{ 'is-invalid': form.errors.has('is_hostel') }">
                        <option value="Y">Yes</option>
                        <option value="N">No</option>
                      </select>
                      <div class="error" v-if="form.errors.has('is_hostel')" v-html="form.errors.get('is_hostel')" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Select Status</label>
                      <select name="status" id="status" class="form-control" v-model="form.status" :class="{ 'is-invalid': form.errors.has('status') }">
                        <option value="Y">Active</option>
                        <option value="N">InActive</option>
                      </select>
                      <div class="error" v-if="form.errors.has('status')" v-html="form.errors.get('status')" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead>
                      <tr>
                        <th>SN</th>
                        <th>Session</th>
                        <th>Expected Payment Date</th>
                        <th>Payment Mode</th>
                        <th>Category</th>
                        <th class="text-center">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, i) in schedules" :key="item.session_fee_id" v-if="schedules.length">
                        <th scope="row">{{ ++i }}</th>
                        <td>{{ item.session }}</td>
                        <td>{{ item.expected_payment_date }}</td>
                        <td>{{ item.year }}</td>
                        <td>{{ item.category }}</td>
                        <td class="text-right">{{ item.currency_symbol }}{{ item.amount }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">Close</button>
              <button :disabled="form.busy" type="submit" class="btn btn-primary">{{ editMode ? "Update" : "Create" }} Student</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import moment from "moment";
import {baseurl} from '../../base_url'
export default {
  name: "List",
  components: {
    Datepicker
  },
  data() {
    return {
      students: [],
      sessions: [],
      schedules: [],
      categories: [],
      session:{},
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      query: "",
      editMode: false,
      isLoading: false,
      form: new Form({
        student_id :'',
        first_name:'',
        last_name:'',
        roll_no:'',
        email:'',
        mobile:'',
        date_of_birth:'',
        nid:'',
        address:'',
        nationality:'',
        session_id:'',
        category_id:'',
        status:'',
        is_hostel:'',
        batch_number:'',
        schedule_data: []
      }),
      sessionId:'',
      roll_number:'',
    }
  },
  watch: {
    query: function(newQ, old) {
      if (newQ === "") {
        this.getAllStudent();
      } else {
        this.searchData();
      }
    }
  },
  mounted() {
    document.title = 'Student List | Bill';
    this.getAllStudent();
    this.getAllSession();
  },
  methods: {
    getAllStudent(){
      axios.get(baseurl+ 'api/student?page='+ this.pagination.current_page
          + "&sessionId=" + this.sessionId
          + "&roll_number=" + this.roll_number
      ).then((response)=>{
        this.students = response.data.data;
        this.pagination = response.data.meta;
      }).catch((error)=>{

      })
    },
    searchData(){
      axios.get(baseurl+"api/search/student/" + this.query + "?page=" + this.pagination.current_page).then(response => {
        this.students = response.data.data;
        this.pagination = response.data.meta;
      }).catch(e => {
        this.isLoading = false;
      });
    },
    reload(){
      this.getAllStudent();
      this.query = "";
      this.$toaster.success('Data Successfully Refresh');
    },
    closeModal(){
      $("#StudentModelModal").modal("hide");
    },
    createStudentModel(){
      this.getAllCategory();
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      this.schedules = '';
      this.form.schedule_data = '';
      $("#StudentModelModal").modal("show");
    },
    store(){
      this.form.busy = true;
      this.form.post(baseurl+ "api/student").then(response => {
        console.log(response)
        $("#StudentModelModal").modal("hide");
        this.getAllStudent();
      }).catch(e => {
        this.$toaster.error('Already Added');
        this.isLoading = false;
      });
    },
    edit(role) {
      this.form.student_id = role.student_id
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      this.form.fill(role);
      this.LoadStudentWiseBillScheduleData();
      this.getAllCategory();
      $("#StudentModelModal").modal("show");
    },
    update(){
      this.form.busy = true;
      this.form.put(baseurl+"api/student/" + this.form.student_id).then(response => {
        $("#StudentModelModal").modal("hide");
        this.getAllStudent();
      }).catch(e => {
        this.isLoading = false;
      });
    },
    destroy(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(baseurl+'api/student/'+ id).then((response)=>{
            this.getAllStudent();
            Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
          })
        }
      })
    },
    LoadScheduleData(){
      axios.get(baseurl + "api/get-schedule-data/" + this.form.session_id + "/" + this.form.category_id).then(response => {
        this.schedules = response.data.data.data;
        this.form.schedule_data = response.data.data.data;
        this.form.batch_number = response.data.session.batch_number;
      }).catch(e => {
        this.isLoading = false;
      });
    },
    LoadStudentWiseBillScheduleData(){
      axios.get(baseurl + "api/get-student-wise-schedule-data/" + this.form.student_id).then(response => {
        this.schedules = response.data.data;
        this.form.schedule_data = response.data.data;
      }).catch(e => {
        this.isLoading = false;
      });
    },
    getAllSession(){
      axios.get(baseurl+'api/get-all-session').then((response)=>{
        this.sessions = response.data.sessions;
      }).catch((error)=>{

      })
    },
    getAllCategory(){
      axios.get(baseurl+'api/get-all-category').then((response)=>{
        this.categories = response.data.categories;
      }).catch((error)=>{

      })
    },
    customFormatter(date) {
      return moment(date).format('YYYY-MM-DD');
    }
  },
}
</script>

<style scoped>

</style>
