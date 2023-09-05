<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Slider List']"/>
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <div class="datatable" v-if="!isLoading">
              <div class="card-body">
                <div class="d-flex">
                  <div class="flex-grow-1">
                    <div class="row">
                      <div class="col-md-2">
                        <input v-model="query" type="text" class="form-control" placeholder="Search">
                      </div>
                    </div>
                  </div>
                  <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" @click="createSlider">
                      <i class="fas fa-plus"></i>
                      Add Slider
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
                        <th>Slider Name</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
<!--                      <tr v-for="(category, i) in categories" :key="category.student_bill_id" v-if="categories.length">-->
<!--                        <th scope="row">{{ ++i }}</th>-->
<!--                        <td>{{ category.name }}</td>-->
<!--                        <td>-->
<!--                          <button @click="edit(category)" class="btn btn-success btn-sm"><i class="far fa-edit"></i></button>-->
<!--                          <button @click="destroy(category.id)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>-->
<!--                        </td>-->
<!--                      </tr>-->
                    </tbody>
                  </table>
                  <br>
                  <pagination
                      v-if="pagination.last_page > 1"
                      :pagination="pagination"
                      :offset="5"
                      @paginate="query === '' ? getAllSlider() : searchData()"
                  ></pagination>
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
    <div class="modal fade" id="SliderModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title mt-0" id="myLargeModalLabel">{{ editMode ? "Edit" : "Add" }} Slider</h5>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" @click="closeModal">×</button>
          </div>
          <form @submit.prevent="editMode ? update() : store()" @keydown="form.onKeydown($event)">
            <div class="modal-body">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Slider Name</label>
                      <input type="text" name="name" v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                      <div class="error" v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">Close</button>
              <button :disabled="form.busy" type="submit" class="btn btn-primary">{{ editMode ? "Update" : "Create" }} Slider</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
export default {
  name: "List",
  data() {
    return {
      sliders: [],
      pagination: {
        current_page: 1
      },
      query: "",
      editMode: false,
      isLoading: false,
      form: new Form({
        id :'',
        name :'',
      }),
    }
  },
  watch: {
    query: function(newQ, old) {
      if (newQ === "") {
        this.getAllSlider();
      } else {
        this.searchData();
      }
    }
  },
  mounted() {
    document.title = 'Slider List | Bill';
    this.getAllSlider();
  },
  methods: {
    getAllSlider(){
      this.isLoading = true;
      axios.get(baseurl + 'api/sliders?page='+ this.pagination.current_page).then((response)=>{
        this.sliders = response.data.data;
        this.pagination = response.data.meta;
        this.isLoading = false;
      }).catch((error)=>{

      })
    },
    searchData(){
      axios.get(baseurl + "api/search/sliders/" + this.query + "?page=" + this.pagination.current_page).then(response => {
        this.sliders = response.data.data;
        this.pagination = response.data.meta;
      }).catch(e => {
        this.isLoading = false;
      });
    },
    reload(){
      this.getAllSlider();
      this.query = "";
      this.$toaster.success('Data Successfully Refresh');
    },
    closeModal(){
      $("#SliderModal").modal("hide");
    },
    createSlider(){
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      $("#SliderModal").modal("show");
    },
    store(){
      this.form.busy = true;
      this.form.post(baseurl + "api/sliders").then(response => {
        $("#SliderModal").modal("hide");
        this.getAllSlider();
      }).catch(e => {
        this.isLoading = false;
      });
    },
    edit(category) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      this.form.fill(category);
      $("#SliderModal").modal("show");
    },
    update(){
      this.form.busy = true;
      this.form.put(baseurl + "api/sliders/" + this.form.id).then(response => {
        $("#SliderModal").modal("hide");
        this.getAllSlider();
      }).catch(e => {
        this.isLoading = false;
      });
    },
    destroy(id){
      Swal.fire({
        title: 'You Can not Delete this Category.Because This Category Related To another module',
        // text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        // confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        // if (result.isConfirmed) {
        //   axios.delete(baseurl + 'api/sliders/'+ id).then((response)=>{
        //     this.getAllCategory();
        //     Swal.fire(
        //         'Deleted!',
        //         'Your file has been deleted.',
        //         'success'
        //     )
        //   })
        // }
      })
    },
  },
}
</script>

<style scoped>

</style>
