<template>
  <div class="container-fluid">
    <breadcrumb :options="['Payment Approval']"></breadcrumb>
    <div class="card">
      <div class="card-body">
        <div class="table-condensed offset-3 col-md-9">
          <div id="printDiv" ref="printMe">
            <div style="width: 70%;">
              <p id="success">Payment approval in AMS was successful for following advances:</p>
              <table class="table table-sm m-0 small">
                <thead class="thead-dark table-bordered">
                <tr>
                  <th>Advance ID</th>
                  <th>Sub-Ledger ID</th>
                  <th>Sub-ledger Name</th>
                  <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(field,index) in data" :key="index">
                  <td>{{ field.AdvanceID }}</td>
                  <td>{{ field.SubLedgerID }}</td>
                  <td>{{ field.ResStaffName }}</td>
                  <td>{{ field.Amount }}</td>
                </tr>
                </tbody>
              </table>
            </div>
            <div style="margin-left: 50px;">
              <p style="margin:0;font-weight:bold;">Date: {{moment().format('DD-MM-YYYY')}}</p>
              <p style="margin:0;font-weight:bold;">Time: {{moment().format('hh:mm a')}}</p>
              <p style="margin:0;font-weight:bold;">User Name: {{me.StaffName}}</p>
            </div>
          </div>
        </div>
        <div class="print-button">
<!--          <button class="btn btn-warning" @click="pdf">Save as PDF</button>-->
          <button class="btn btn-warning" @click="print">Print</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import { jsPDF } from "jspdf";
import moment from "moment"

export default {
  mixins: [Common],
  data() {
    return {
      data: [],
      output: '',
    }
  },
  created() {
    this.getData();
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosPost('advance/approve/success', {
        data: JSON.parse(this.$route.params.data)
      }, function (response) {
        instance.data = response.data;
      }, function (error) {

      });
    },
    print() {
      $("#printDiv").printThis({
        importCSS: true,
        importStyle: true,
        loadCSS: '',
        copyTagClasses: true,
        copyTagStyles: true,
      });
    },
    async pdf() {
      var el = document.getElementById('printDiv').innerHTML;
      let instance = this;
      this.axiosGet('advance/pdf-print?data='+el,function (response) {

      },function (error){

      });
      // const el = this.$refs.printMe;
      // var currentdate = new Date();
      // var fileName = `${currentdate.getDate()}${currentdate.getMonth()}${currentdate.getFullYear()}`;
      // const options = {
      //   type: 'dataURL'
      // }
      // var canvas = await this.$html2canvas(el, options);
      // console.log(canvas)
      // this.output = await this.$html2canvas(el, options);
      // let pdf = new jsPDF('p', 'pt','a4',false);
      // pdf.addImage(canvas, 'png', 0, 0, 600,200,'','MEDIUM');
      // pdf.save(`${fileName}.pdf`);
    }
  }
}
</script>

<style scoped>

th {
  font-size: 10px;
  text-align: center;
}

td {
  text-align: center;
}

input, textarea, select {
  font-size: 10px;
  padding: 3px;
}

.error {
  color: red;
  font-size: 10px;
}

.table > tbody > tr > td {
  padding: 0 !important;
}

#success {
  text-align: center;
  font-weight: bold;
  font-size: 12px;
}

#printDiv {
  padding: 15px;
  margin-top: 45px;
  display: flex;
}

.print-button {
  text-align: center;
  margin-top: 50px;
}

.print-button button {
  width: 150px;
}
</style>
