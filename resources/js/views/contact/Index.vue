<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Contact']"/>
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <div class="datatable" v-if="!isLoading">
              <div class="card-body">
                <div class="d-flex">
<!--                  <div class="flex-grow-1">-->
<!--                    <div class="row">-->
<!--                      <div class="col-md-2">-->
<!--                        <input v-model="query" type="text" class="form-control" placeholder="Search">-->
<!--                      </div>-->
<!--                    </div>-->
<!--                  </div>-->
                  <div class="card-tools">
<!--                    <button type="button" class="btn btn-success btn-sm" @click="createVolunteer">-->
<!--                      <i class="fas fa-plus"></i>-->
<!--                      Add Volunteer-->
<!--                    </button>-->
<!--                    <button type="button" class="btn btn-primary btn-sm" @click="reload">-->
<!--                      <i class="fas fa-sync"></i>-->
<!--                      Reload-->
<!--                    </button>-->
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead>
                    <tr>
                      <th>SN</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Subject</th>
                      <th>Message</th>
                      <th>Contact Date</th>
<!--                      <th>Action</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(contact, i) in contacts" :key="contact.id" v-if="contacts.length">
                      <th class="text-center" scope="row">{{ ++i }}</th>
                      <td class="text-left">{{ contact.name }}</td>
                      <td class="text-left">{{ contact.email }}</td>
                      <td class="text-left">{{ contact.subject }}</td>
                      <td class="text-left">{{ contact.message }}</td>
                      <td class="text-left">{{ contact.created_at }}</td>
<!--                      <td class="text-center">-->
<!--                        <button @click="edit(contact)" class="btn btn-success btn-sm"><i class="far fa-edit"></i></button>-->
<!--                        <button @click="destroy(contact.id)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>-->
<!--                      </td>-->
                    </tr>
                    </tbody>
                  </table>
                  <br>
                  <pagination
                      v-if="pagination.last_page > 1"
                      :pagination="pagination"
                      :offset="5"
                      @paginate="query === '' ? getAllContact() : searchData()"
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

  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {VueEditor} from "vue2-editor";

export default {
  components: {
    VueEditor
  },
  data() {
    return {
      contacts: [],
      pagination: {
        current_page: 1
      },
      query: "",
      editMode: false,
      isLoading: false,
    }
  },
  watch: {
    query: function(newQ, old) {
      if (newQ === "") {
        this.getAllContact();
      } else {
        this.searchData();
      }
    }
  },
  mounted() {
    document.title = 'Contact List | Baitulaman';
    this.getAllContact();
  },
  methods: {
    getAllContact(){
      axios.get(baseurl + 'api/get-all-contact?page='+ this.pagination.current_page).then((response)=>{
        this.contacts = response.data.data;
        this.pagination = response.data.meta;
      }).catch((error)=>{

      })
    },
    searchData(){
      axios.get(baseurl + "api/search/contacts/" + this.query + "?page=" + this.pagination.current_page).then(response => {
        this.contacts = response.data.data;
        this.pagination = response.data.meta;
      }).catch(e => {
        this.isLoading = false;
      });
    },
    reload(){
      this.getAllContact();
      this.query = "";
      this.$toaster.success('Data Successfully Refresh');
    },
    closeModal(){
      $("#VolunteerModal").modal("hide");
    },
    createVolunteer(){
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      $("#VolunteerModal").modal("show");
    },
    store(){
      this.form.busy = true;
      this.form.post(baseurl + "api/contacts").then(response => {
        $("#VolunteerModal").modal("hide");
        this.getAllContact();
      }).catch(e => {
        this.isLoading = false;
      });
    },
    edit(volunteer) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      this.form.fill(volunteer);
      $("#VolunteerModal").modal("show");
    },

    update(){
      this.form.busy = true;
      this.form.put(baseurl + "api/contacts/" + this.form.id).then(response => {
        $("#VolunteerModal").modal("hide");
        this.getAllContact();
      }).catch(e => {
        this.isLoading = false;
      });
    },
    destroy(id) {
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
          axios.delete('api/contacts/' + id).then((response) => {
            this.getAllContact();
            Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
          })
        }
      })
    }
  },
}
</script>

<style scoped>

</style>
