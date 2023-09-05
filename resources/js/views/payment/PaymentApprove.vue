<template>
  <div class="container-fluid">
    <breadcrumb :options="['Payment Approval']"></breadcrumb>
    <div class="card">
      <div class="card-body">
        <div class="table-condensed">
          <form class="form-horizontal" id="form" @submit.prevent="onSubmit">
            <div class="row" style="margin-bottom:10px;">
              <div class="col-md-5">
                <label>Voucher Type</label>
                <multiselect v-model="voucherType" :options="voucherTypes" :multiple="false" :close-on-select="true"
                             :clear-on-select="false" :preserve-search="true" placeholder="Voucher Type"
                             label="HeadName" track-by="GLCodeID">

                </multiselect>
                <span class="error" v-if="voucherType_error !== ''">{{ voucherType_error }}</span>
              </div>
            </div>
            <table class="table table-sm m-0 small">
              <thead class="thead-dark">
              <tr>
                <th>Advance ID</th>
                <th>Debit Account</th>
                <th>Sub-ledger ID</th>
                <th>Advance For Business/Function</th>
                <th>Amount</th>
                <th>Purpose Of Advance</th>
                <th>Payee Name</th>
                <th>Account No.</th>
                <th>Bank Name</th>
                <th>Branch Name</th>
                <th>Routing No.</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field,index) in fields" :key="index">
                <td>
                  {{field.advanceId}}
                </td>
                <td>
                  <select class="form-control" id="debit_account" v-model="field.debitAccount">
                    <option value="">Select</option>
                    <option v-for="(debit,index) in field.debitAccounts" :value="debit.GLCodeID">{{debit.AccountCode}} ({{debit.HeadName}})</option>
                  </select>
                  <span class="error" v-if="errors[index] !== undefined && errors[index].debitAccount !== undefined">{{
                      errors[index].debitAccount
                    }}</span>
                </td>
                <td>
                  {{field.subLedgerId}}
                </td>
                <td>
                  <select class="form-control" id="business" v-model="field.business" @change="changeBusiness($event,index)">
                    <option value="">Select</option>
                    <option :value="bus.Business" v-for="(bus,index) in business" :key="index">
                      {{ bus.BusinessName }}
                    </option>
                  </select>
                  <span class="error" v-if="errors[index] !== undefined && errors[index].business !== undefined">{{
                      errors[index].business
                    }}</span>
                </td>
                <td>
                  <input type="text" class="form-control" id="amount" v-model="field.amount" max="50">
                  <span class="error" v-if="errors[index] !== undefined && errors[index].amount !== undefined">{{
                      errors[index].amount
                    }}</span>
                </td>
                <td>
                  <textarea style="height:28px" id="purpose" class="form-control" v-model="field.purpose"
                            placeholder="Purpose" max="255"></textarea>
                  <span class="error"
                        v-if="errors[index] !== undefined && errors[index].purpose !== undefined">{{
                      errors[index].purpose
                    }}</span>
                </td>
                <td>
                  <input type="text" class="form-control" id="business" v-model="field.payee" max="50">
                  <span class="error"
                        v-if="errors[index] !== undefined && errors[index].payee !== undefined">{{
                      errors[index].payee
                    }}</span>
                </td>
                <td>
                  <input type="text" class="form-control" id="account_number" v-model="field.accountNumber" max="50" :disabled="field.paymentMode === 'ACPayee'">
                  <span class="error"
                        v-if="errors[index] !== undefined && errors[index].payee !== undefined">{{
                      errors[index].accountNumber
                    }}</span>
                </td>
                <td>
                  <select class="form-control" id="bank" v-model="field.bank">
                    <option value="">Select</option>
                    <option :value="bank.BankID" v-for="(bank,index) in banks" :key="index">
                      {{ bank.BankName }}
                    </option>
                  </select>
                  <span class="error" v-if="errors[index] !== undefined && errors[index].bank !== undefined">{{
                      errors[index].bank
                    }}</span>
                </td>
                <td>
                  <input type="text" class="form-control" id="branch" v-model="field.branch" max="50" :disabled="field.paymentMode === 'ACPayee'">
                </td>
                <td>
                  <input type="text" class="form-control" id="routing_no" v-model="field.routingNo" max="50" :disabled="field.paymentMode === 'ACPayee'">
                </td>
              </tr>
              </tbody>
            </table>
            <div class="row submit-button">
              <button class="btn btn-gradient" type="submit">Approve</button>
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

export default {
  mixins: [Common],
  data() {
    return {
      fields: [],
      business: [],
      banks: [],
      voucherTypes: [],
      advanceTypes: [],
      voucherType: "",
      voucherType_error: "",
      errors: [],
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosPost('advance/approve/data', {
        data: JSON.parse(this.$route.params.data)
      },function (response) {
        instance.advanceTypes = response.advanceTypes
        response.data.forEach(function(item) {
          instance.fields.push({
            advanceId: item.AdvanceID,
            debitAccount: '',
            debitAccounts: instance.loadGLCode(item.AdvanceForBusiness),
            subLedgerId: item.ResStaffID.charAt(0) === 'C' ? 'CO-'+item.ResStaffID : 'MA-'+item.ResStaffID,
            business: item.AdvanceForBusiness,
            amount: item.Amount,
            purpose: item.Purpose,
            payee: item.Payee,
            paymentMode: item.PaymentMode,
            accountNumber: item.AccountNo,
            bank: item.BankID,
            branch: item.BranchName,
            routingNo: item.RoutingNo
          });
        });
        instance.business = response.business;
        instance.banks = response.banks;
        instance.voucherTypes = response.voucherTypes;
      }, function (error) {

      });
    },
    loadGLCode(business) {
      let selected = this.filterGLCode(business)
      if (selected.length === 0) {
        return this.filterGLCode('5')
      } else {
        return selected
      }
    },
    filterGLCode(business) {
      return this.advanceTypes.filter(function (item) {
        return item.Business === business;
      });
    },
    changeBusiness(e,key) {
      let selected = this.filterGLCode(e.target.value);
      if (selected.length === 0) {
        this.fields[key].debitAccounts = this.filterGLCode('5')
      } else {
        this.fields[key].debitAccounts = selected
      }
    },
    checkFieldValue() {
      this.errors = [];
      let instance = this;
      this.fields.forEach(function (item, index) {
        if (item.purpose === '' || item.payee === '' || item.payee === undefined || item.amount === '' || item.business === ''
            || item.debitAccount === '') {
          instance.errors[index] = {
            purpose: item.purpose === '' ? 'Purpose is required' : '',
            payee: item.payee === '' || item.payee === undefined ? 'Name is required' : '',
            amount: item.amount === '' ? 'Amount is required' : '',
            business: item.business === '' ? 'Business is required' : '',
            debitAccount: item.debitAccount === '' ? 'Debit account is required' : '',
          };
        }
      });
      if (this.voucherType === '') {
        this.voucherType_error = 'Voucher type is required'
      } else {
        this.voucherType_error = ''
      }
    },
    onSubmit() {
      this.checkFieldValue();
      if (this.errors.length === 0 && this.voucherType_error === '') {
        this.postData();
      }
    },
    postData() {
      let instance = this;
      this.axiosPost('advance/approve', {
        fields: instance.fields,
        voucherType: instance.voucherType
      }, function (response) {
        instance.errors = [];
        instance.voucherType_error = '';
        instance.successNoti(response.message);
        instance.$router.push(baseurl+'finance/payment/approve/success/'+instance.$route.params.data)
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
  font-size: 12px;
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