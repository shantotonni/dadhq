<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Program List']"/>
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
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead>
                    <tr>
                      <th>SN</th>
                      <th>Program Name</th>
                      <th>Description</th>
                      <th>User</th>
                      <th>Program Date</th>
                      <th>Program Time</th>
                      <th>Image</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(program, i) in programs" :key="program.id" v-if="programs.length">
                      <th class="text-center" scope="row">{{ ++i }}</th>
                      <td class="text-left">{{ program.program.title }}</td>
                      <td class="text-left" v-html="program.program.description" width="50%"></td>
                      <td class="text-left">{{ program.customer.first_name }}  {{ program.customer.last_name }}</td>
                      <td class="text-right">{{ program.program.program_date }}</td>
                      <td class="text-right">{{ program.program.program_time }}</td>
                      <td class="text-left">
                        <img v-if="program.program.image" height="40" width="40" :src="tableImage(program.program.image)" alt="">
                      </td>
                    </tr>
                    </tbody>
                  </table>
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

export default {
  data() {
    return {
      programs: [],
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
        this.getAllUserProgram();
      } else {
        this.searchData();
      }
    }
  },
  mounted() {
    document.title = 'User Program List | DadHQ';
    this.getAllUserProgram();
  },
  methods: {
    getAllUserProgram(){
      this.isLoading = true;
      axios.get(baseurl + 'api/user-programs?page='+ this.pagination.current_page).then((response)=>{
        this.programs = response.data.programs;
        this.pagination = response.data.meta;
        this.isLoading = false;
      }).catch((error)=>{

      })
    },
    searchData(){
      axios.get(baseurl + "api/search/programs/" + this.query + "?page=" + this.pagination.current_page).then(response => {
        this.programs = response.data.data;
        this.pagination = response.data.meta;
      }).catch(e => {
        this.isLoading = false;
      });
    },
    reload(){
      this.getAllUserProgram();
      this.query = "";
      this.$toaster.success('Data Successfully Refresh');
    },
    tableImage(image) {
      return window.location.origin + "/images/program/" + image;
    },
  },
}
</script>

<style scoped>

</style>
