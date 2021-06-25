<template>
  <div>
    <div class="container">
      <div class="row mt-3">
        <div class="col-md-6 col-md-12 text-right">
          <ui-button
            color="primary"
            icon="add"
            icon-position="left"
            size="normal"
            raised
            @click="openModal('contactModal', 'Add Contact')"
            >Add Contact</ui-button
          >
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-md-6 col-md-12">
          <vuetable
            ref="vuetable"
            :api-mode="false"
            :fields="fields"
            :per-page="perPage"
            :data-manager="dataManager"
            pagination-path="pagination"
            @vuetable:pagination-data="onPaginationData"
            :css="css.table"
          >
            <div slot="#" slot-scope="props">
              {{ props.rowIndex + 1 + (currentPage - 1) * perPage }}
            </div>
            <div slot="action" slot-scope="props">
              <ui-icon-button
                color="green"
                class="material-icons"
                icon="edit"
                size="small"
                tooltip="Edit"
                tooltip-position="bottom"
                @click="showEdit(props.rowData.id)"
              ></ui-icon-button>

              <ui-icon-button
                color="red"
                class="material-icons"
                icon="delete"
                size="small"
                tooltip="Delete"
                tooltip-position="bottom"
                @click="showConfirm('deleteConfirm', props.rowData.id)"
              ></ui-icon-button>
            </div>
          </vuetable>
          <div style="padding-top: 10px">
            <vuetable-pagination
              ref="pagination"
              :css="css.pagination"
              @vuetable-pagination:change-page="onChangePage"
            ></vuetable-pagination>
          </div>
        </div>
      </div>
      <!-- Contact Modal Starts -->
      <ui-modal
        ref="contactModal"
        :title="title"
        size="large"
        transition="scale-up"
        @close="resetContactForm"
      >
        <form
          class="px-3 py-3"
          autocomplete="off"
          method="POST"
          @submit.prevent="isEdit ? updateContact() : createContact()"
        >
          <div class="row">
            <div class="col-md-6 mt-1">
              <ui-textbox
                floating-label
                icon="account_box"
                class="material-icons"
                label="Name"
                placeholder="Enter name"
                type="text"
                data-vv-as="Name"
                name="name"
                v-validate="'required|alpha_spaces|min:3|max:35'"
                v-model="contact.name"
              ></ui-textbox>
              <p class="text-danger ml-4" v-if="errors.has('name')">
                {{ errors.first("name") }}
              </p>
            </div>
            <div class="col-md-6 mt-1">
              <ui-textbox
                floating-label
                icon="phone"
                class="material-icons"
                label="Phone Number"
                placeholder="Enter phone number"
                type="tel"
                data-vv-as="Phone Number"
                name="phone"
                v-validate="'required|numeric|min:10|max:12'"
                v-model="contact.phone"
              ></ui-textbox>
              <p class="text-danger ml-4" v-if="errors.has('phone')">
                {{ errors.first("phone") }}
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mt-1">
              <ui-textbox
                floating-label
                icon="mail"
                class="material-icons"
                label="Email Id"
                placeholder="Enter email id"
                type="email"
                data-vv-as="Email Id"
                name="email"
                v-validate="'required|email|max:150'"
                v-model="contact.email"
              ></ui-textbox>
              <p class="text-danger ml-4" v-if="errors.has('email')">
                {{ errors.first("email") }}
              </p>
            </div>
            <div class="col-md-6 mt-1">
              <ui-textbox
                floating-label
                icon="home"
                class="material-icons"
                label="Address"
                placeholder="Enter address"
                type="text"
                data-vv-as="Address"
                name="address"
                v-validate="'required|max:100'"
                v-model="contact.address"
                :multi-line="true"
                :maxlength="100"
                enforce-maxlength
              ></ui-textbox>
              <p class="text-danger ml-4" v-if="errors.has('address')">
                {{ errors.first("address") }}
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mt-1">
              <ui-textbox
                floating-label
                icon="notes"
                class="material-icons"
                label="Bio"
                placeholder="Enter bio"
                type="text"
                data-vv-as="Bio"
                name="bio"
                v-validate="'required|max:150'"
                v-model="contact.bio"
                :multi-line="true"
                :maxlength="150"
                enforce-maxlength
              ></ui-textbox>
              <p class="text-danger ml-4" v-if="errors.has('bio')">
                {{ errors.first("bio") }}
              </p>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-8 text-right">
              <ui-button
                color="green"
                class="material-icons"
                icon="done"
                size="normal"
                raised
                >{{ isEdit ? "Update" : "Submit" }}
              </ui-button>
              <ui-button
                color="red"
                class="material-icons"
                icon="clear"
                size="normal"
                raised
                buttonType="button"
                @click="resetContactForm"
                >Cancel</ui-button
              >
            </div>
          </div>
        </form>
      </ui-modal>
      <!-- Contact Modal Ends -->

      <!-- confirm Delete Starts -->
      <ui-confirm
        confirm-button-icon="delete"
        confirm-button-text="Delete"
        deny-button-text="Cancel"
        ref="deleteConfirm"
        title="Delete"
        type="danger"
        @confirm="deleteContact"
      >
        Are you sure you want to delete this contact?
      </ui-confirm>
      <!-- Confirm Delete Ends -->

      <!-- Snackbar Starts -->
      <ui-snackbar-container
        ref="snackbarContainer"
        position="center"
      ></ui-snackbar-container>
      <!-- Snackbar Ends -->
    </div>
  </div>
</template>

<script>
import { Vuetable, VuetablePagination } from "vuetable-2";

export default {
  components: {
    Vuetable,
    VuetablePagination,
  },

  data() {
    return {
      contact: {
        name: "",
        phone: "",
        email: "",
        address: "",
        bio: "",
      },
      title: "",
      isEdit: false,
      contacts: [],
      contactToUpdate: {},
      contactToDelete: null,
      loading: true,
      perPage: 10,
      fields: [
        {
          name: "name",
          title: "Name",
        },
        {
          name: "phone",
          title: "Phone",
        },
        {
          name: "email",
          title: "Email",
        },
        {
          name: "address",
          title: "Address",
        },
        {
          name: "bio",
          title: "Bio",
        },
        {
          name: "action",
        },
      ],
      css: {
        pagination: {
          infoClass: "pull-left",
          wrapperClass: "vuetable-pagination pull-right",
          activeClass: "btn-primary",
          disabledClass: "disabled",
          pageClass: "btn btn-border",
          linkClass: "btn btn-border",
          icons: {
            first: "",
            prev: "",
            next: "",
            last: "",
          },
        },
        table: {
          tableWrapper: "",
          tableBodyClass: "vuetable-semantic-no-top fixed",
          tableClass: "ui blue selectable unstackable celled table table-hover",
          loadingClass: "loading",
        },
      },
    };
  },

  watch: {
    contacts(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
  },
  mounted() {
    this.fetchData();
  },

  methods: {
    onPaginationData(paginationData) {
      this.$refs.pagination.setPaginationData(paginationData);
    },
    onChangePage(page) {
      this.$refs.vuetable.changePage(page);
    },
    dataManager(sortOrder, pagination) {
      let data = this.contacts;

      // account for search filter
      if (this.searchFor) {
        // the text should be case insensitive
        let txt = new RegExp(this.searchFor, "i");

        // search on name, email, and nickname
        data = _.filter(data, function (item) {
          return (
            item.name.search(txt) >= 0 ||
            item.email.search(txt) >= 0 ||
            item.nickname.search(txt) >= 0
          );
        });
      }

      // sortOrder can be empty, so we have to check for that as well
      if (sortOrder.length > 0) {
        console.log("orderBy:", sortOrder[0].sortField, sortOrder[0].direction);
        data = _.orderBy(data, sortOrder[0].sortField, sortOrder[0].direction);
      }

      // since the filter might affect the total number of records
      // we can ask Vuetable to recalculate the pagination for us
      // by calling makePagination(). this will make VuetablePagination
      // work just like in API mode
      pagination = this.$refs.vuetable.makePagination(data.length);

      // if you don't want to use pagination component, you can just
      // return the data array
      return {
        pagination: pagination,
        data: _.slice(data, pagination.from - 1, pagination.to),
      };
    },

    fetchData() {
      axios.get("/contacts").then((response) => {
        this.contacts = response.data.contacts.reverse();
        this.loading = false;
      });
    },

    openModal(ref, modalTitle) {
      this.title = modalTitle;
      this.$refs[ref].open();
    },
    closeModal(ref) {
      this.$refs[ref].close();
    },

    showConfirm(ref, id) {
      // get & set selected contact id
      this.contactToDelete = id;
      this.$refs[ref].open();
    },
    deleteContact() {
      let localThis = this;
      let message;
      axios
        .delete("/contacts/" + this.contactToDelete)
        .then((response) => {
          this.contacts = this.contacts.filter(function (item) {
            return item.id != localThis.contactToDelete;
          });
          message = "Contact deleted successfully!";
          this.createSnackBar(message);
        })
        .catch((error) => {
          message = "Oops! Something went wrong.";
          this.createSnackBar(message);
          console.log(error);
        });
    },

    createContact() {
      this.$validator.validate().then((isValid) => {
        if (!isValid) {
          return;
        }
        let message;
        let contactData = this.contact;
        axios
          .post("/contacts", contactData)
          .then((response) => {
            this.contacts.unshift(response.data.contact);
            message = "Contact added successfully!";
            this.createSnackBar(message);
          })
          .catch((error) => {
            message = "Oops! Unable to add contact.";
            this.createSnackBar(message);
            console.log(error);
          });
      });
    },

    showEdit(contactId) {
      this.isEdit = true;
      this.contactToUpdate = contactId;
      this.getParticularData(contactId);
    },
    getParticularData(contactId) {
      axios
        .get("/contacts/" + contactId)
        .then((response) => {
          let particularData = response.data;
          this.openModal("contactModal", "Edit Contact");
          this.contact = particularData[0];
        })
        .catch((error) => {
          console.log(error);
        });
    },
    updateContact() {
      this.$validator.validate().then((isValid) => {
        if (!isValid) {
          return;
        }
        let message;
        let contactData = this.contact;
        axios
          .put("/contacts/" + this.contactToUpdate, contactData)
          .then((response) => {
            this.fetchData();
            message = "Contact update successfully!";
            this.createSnackBar(message);
          })
          .catch((error) => {
            message = "Oops! Unable to update contact.";
            this.createSnackBar(message);
            console.log(error);
          });
      });
    },

    createSnackBar(message) {
      this.resetContactForm();
      //show snackbar on crud
      this.$refs.snackbarContainer.createSnackbar({
        message: message,
        action: "",
        duration: 5000,
      });
    },
    resetContactForm() {
      this.closeModal("contactModal");
      this.contact.last_name = "";
      this.contact.phone = "";
      this.contact.email = "";
      this.contact.address = "";
      this.contact.bio = "";
      this.isEdit = false;

      //reset validation
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach((field) => field.reset());
        this.$validator.fields.items.forEach((field) =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
  },
};
</script>

<style scoped>
.vuetable-pagination {
  float: right;
}
</style>
