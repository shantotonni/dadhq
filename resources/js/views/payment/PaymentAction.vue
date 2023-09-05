<template>
  <div class="container-fluid">
    <breadcrumb :options="['Payment Action']"></breadcrumb>
    <div class="card">
      <div class="card-body">
        <div class="table-condensed">
          <form class="form-horizontal" id="form" @submit.prevent="onSubmit">
            <table class="table table-sm m-0 small">
              <thead class="thead-dark">
              <tr>
                <th>Requisition Date</th>
                <th>Requisition ID</th>
                <th>Advance ID</th>
                <th>Responsible Staff</th>
                <th>Pay To</th>
                <th>Purpose Of Advance</th>
                <th>Advance Amount</th>
                <th>Action <span class="required-field">*</span></th>
                <th>Reason <span class="required-field">*</span></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field,index) in fields" :key="index">
                <td>
                  {{field.requisitionDate}}
                </td>
                <td>
                  {{field.requisitionId}}
                </td>
                <td>
                  {{field.advanceId}}
                </td>
                <td>
                  {{field.responsibleStaff}}
                </td>
                <td>
                  {{field.payee}}
                </td>
                <td>
                 {{field.purpose}}
                </td>
                <td>
                  {{field.amount}}
                </td>
                <td>
                  <select class="form-control" id="action" v-model="field.action">
                    <option value="">Select</option>
                    <option value="Rejected">Reject</option>
                    <option value="On-hold">On Hold</option>
                    <option value="Returned">Return</option>
                  </select>
                  <span class="error" v-if="errors[index] !== undefined && errors[index].action !== undefined">{{
                      errors[index].action
                    }}</span>
                </td>
                <td>
                  <textarea style="height:28px" id="reason" class="form-control" v-model="field.reason"
                            placeholder="Reason" max="255"></textarea>
                  <span class="error"
                        v-if="errors[index] !== undefined && errors[index].reason !== undefined">{{
                      errors[index].reason
                    }}</span>
                </td>
              </tr>
              </tbody>
            </table>
            <div class="row submit-button">
              <button class="btn btn-gradient" type="submit" style="float:right;">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import moment from "moment";

export default {
  mixins: [Common],
  data() {
    return {
      fields: [],
      errors: [],
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosPost('advance/action/data', {
        data: JSON.parse(this.$route.params.data)
      },function (response) {
        response.data.forEach(function(item) {
          instance.fields.push({
            requisitionDate: moment(item.CreatedAt).format('DD-MM-YYYY'),
            requisitionId: item.RequisitionID,
            advanceId: item.AdvanceID,
            responsibleStaff: item.ResStaffID+'-'+item.ResStaffName,
            payee: item.Payee,
            purpose: item.Purpose,
            amount: item.Amount,
            action: '',
            reason: ''
          });
        });
      }, function (error) {

      });
    },
    checkFieldValue() {
      this.errors = [];
      let instance = this;
      this.fields.forEach(function (item, index) {
        if (item.action === '' || item.reason === '' || item.action === undefined || item.reason === undefined) {
          instance.errors[index] = {
            action: item.action === '' ? 'Action is required' : '',
            reason: item.reason === '' || item.reason === undefined ? 'Reason is required' : ''
          };
        }
      });
    },
    onSubmit() {
      this.checkFieldValue();
      if (this.errors.length === 0) {
        this.postData();
      }
    },
    postData() {
      let instance = this;
      this.axiosPost('advance/reject', {
        fields: instance.fields
      }, function (response) {
        instance.errors = [];
        instance.successNoti(response.message);
        instance.$router.push(baseurl+'finance/payment/list')
      }, function (error) {
        instance.errorNoti(error.response.data.message);
      });
    }
  }
}
</script>

<style scoped>
th {
  font-size: 13px;
  text-align: center;
}

td {
  font-size: 13px;
  text-align: center;
}

.submit-button {
  margin: 25px 0 10px 0;
  float: right;
}

input, textarea, select {
  font-size: 10px;
  padding: 3px;
}

.error {
  color: red;
  font-size: 10px;
}

.card-header {
  background: #333547 !important;
  color: #ffffff;
  text-transform: capitalize;
  letter-spacing: 0.05em;
  font-size: 12px;
}
.table>tbody>tr>td {
  padding: 5px !important;
}
</style>

<style>
.datepicker .picker .picker-content {
  width: 350px !important;
}
.datepicker .input-wrapper {
  padding: 4px !important;
}
.datepicker {
  z-index: 9999 !important;
}
</style>