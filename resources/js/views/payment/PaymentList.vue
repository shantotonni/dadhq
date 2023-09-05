<template>
  <div class="container-fluid">
    <breadcrumb :options="['Payment Approval']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="card-header">
        <span>Please find below advances for approval</span>
      </div>
      <div class="card-body">
        <div class="table-condensed">
          <advanced-datatable :options="tableOptions" :advance="selectedAdvance" :business="business" :department="department" :paymentModes="paymentModes" :banks="banks" :action="selectedAction">
            <template slot="approval" slot-scope="row">
              <input type="checkbox" class="form-control advance" :checked="row.item.checked" :id="row.item.AdvanceID" @change="approveCheck(row.item.AdvanceID,row.item.PaymentModeID,row.item.BankID)"/>
            </template>
            <template slot="action" slot-scope="row">
              <input type="checkbox" class="form-control action" :checked="row.item.checked_action" :id="'ac-'+row.item.AdvanceID" @change="actionCheck(row.item.AdvanceID)"/>
            </template>
          </advanced-datatable>
          <div class="row submit-button">
            <button class="btn btn-gradient" type="submit" @click="onSubmit">Submit</button>
          </div>
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
      selectedAdvance: [],
      selectedPaymentMode: '',
      selectedBankID: '',
      selectedAction: [],
      business: [],
      department: [],
      paymentModes: [],
      banks: [],
      tableOptions: {
        source: 'advance/list',
        search: false,
        filterPayment: true,
        slots: [12, 13],
        hideColumn: ['PaymentModeID','BankID'],
        showFilter: ['requestId','advanceId','reqStaffId','resStaffId','business','department','paymentMode','bank'],
        colSize: ['col-lg-1','col-lg-1','col-lg-1','col-lg-1','col-lg-2','col-lg-2','col-lg-2','col-lg-2'],
        slotsName: ['approval', 'action'],
        pages: [50, 100],
        addHeader: ['Select for Approval', 'Select for Action'],
      },
    }
  },
  created() {
    this.getData();
  },
  methods: {
    approveCheck(value,paymentMode,bankId) {
      //check action checked
      if (this.selectedAction.length > 0) {
        this.selectedAction = [];
        $('.action').prop('checked', false);
      }

      var advance = this.selectedAdvance.find(function (item) {
        return item === value;
      });
      if (advance !== undefined) {
        let instance = this;
        this.selectedAdvance.forEach(function (item, index) {
          if (item === advance) {
            instance.selectedAdvance.splice(index, 1)
          }
        });
      } else {
        if (this.selectedAdvance.length > 0) {
          if (this.selectedPaymentMode !== paymentMode) {
            this.errorNoti("Need to select same Payment Mode");
            $('#'+value).prop('checked', false);
          } else {
            if (this.selectedBankID !== bankId) {
              this.errorNoti("Need to select same Bank");
              $('#'+value).prop('checked', false);
            } else {
              this.selectedAdvance.push(value);
            }
          }
        } else {
          this.selectedAdvance.push(value);
          this.selectedPaymentMode = paymentMode;
          this.selectedBankID = bankId;
        }
      }
    },
    actionCheck(value) {
      //check approve checked
      if (this.selectedAdvance.length > 0) {
        this.selectedAdvance = [];
        $('.advance').prop('checked', false);
      }

      var action = this.selectedAction.find(function(item) {
        return item === value;
      })
      if (action !== undefined) {
        let instance = this;
        this.selectedAction.forEach(function(item,index){
          if (item === action) {
            instance.selectedAction.splice(index,1);
          }
        });
      } else {
        this.selectedAction.push(value);
      }
    },
    getData() {
      let instance = this;
      this.axiosGet('advance/support-data', function (response) {
        instance.business = response.business;
        instance.department = response.department;
        instance.paymentModes = response.paymentModes;
        instance.banks = response.banks;
      }, function (error) {

      });
    },
    onSubmit() {
      if (this.selectedAdvance && this.selectedAdvance.length > 0) {
        this.$router.push(baseurl+'finance/payment/approve/'+JSON.stringify(this.selectedAdvance));
      } else if (this.selectedAction && this.selectedAction.length > 0) {
        this.$router.push(baseurl+'finance/payment/action/'+JSON.stringify(this.selectedAction));
      }
    },
    postData() {
      let instance = this;
      this.axiosPost('requisition/create', {
        fields: instance.fields,
        paymentDate: instance.payment_required_by
      }, function (response) {
        instance.errors = [];
        instance.payment_required_by = '';
        instance.fields = [
          {
            staffId: '',
            staffName: '',
            purpose: '',
            amount: '',
            payment_mode: '',
            payee: '',
            program_date: '',
            due_date: '',
            advance_for_business: '',
            advance_for_business_name: '',
            remarks: '',
            designation: '',
            department: '',
            email: '',
            mobile: '',
            account_number: '',
            bank_id: '',
            banks: [],
            branch_name: '',
            routing_no: ''
          }
        ];
        // instance.successNoti(response.message);
        instance.$router.push(baseurl + 'requisition/' + response.requisitionID + '/invoice');
      }, function (error) {
        instance.errorNoti(error.response.data.message);
      });
    }
  }
}
</script>

<style scoped>
#add-row {
  float: right;
  margin-bottom: 10px;
}

th {
  font-size: 10px;
}

.submit-button {
  margin: 0px 20px 15px 25px;
  float: right;
}

#purpose {
  width: 97%;
}

#payment_mode {
  width: 97%;
}

#payee {
  width: 97%;
}

#staffId {
  width: 97%;
}

#amount {
  width: 97%;
}

#business {
  width: 97%;
}

#remarks {
  width: 97%;
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