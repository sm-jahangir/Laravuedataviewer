<template>
    <div class="container">
        
        <!-- set progressbar -->
        <vue-progress-bar></vue-progress-bar>
        <vue-snotify></vue-snotify>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    
            <div class="card-header">
                <h4 class="card-title">Customers</h4>
                <div class="card-tools" style="position: absolute;right: 1rem;top: .5rem;">
                <button type="button" class="btn btn-info"  @click="create">
                    Add New
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-primary" @click="reload">
                    Reload
                    <i class="fas fa-sync"></i>
                </button>
                </div>
            </div>


            <div class="m-3">
              <div class="row">
                <div class="col-md-2">
                  <strong>Search By :</strong>
                </div>
                <div class="col-md-3">
                  <select v-model="queryFiled" class="form-control" id="fileds">
                    <option value="name">Name</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="address">Address</option>
                    <option value="total">Total</option>
                  </select>
                </div>
                <div class="col-md-7">
                  <input v-model="query" type="text" class="form-control" placeholder="Search">
                </div>
              </div>
            </div>

                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-hover table-borderd table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-show="customers.length" v-for="(customer, index) in customers" :key="customer.id">
                                    <th scope="row">{{index+1}}</th>
                                    <td>
                                      <img style="width:90px;" :src="'../storage/customer/'+customer.image" :alt="customer.image">
                                    </td>
                                    <td>{{customer.name}}</td>
                                    <td>{{customer.email}}</td>
                                    <td>{{customer.phone}}</td>
                                    <td>{{customer.total}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" @click="edit(customer)">Edit</button>
                                        <button type="button"  @click="destroy(customer)" class="btn btn-danger" href="">Deleted</button>
                                    </td>
                                </tr>         
                                <tr v-show="!customers.length">
                                    <td colspan="6">
                                    <div class="alert alert-danger" role="alert">Sorry :( No data found.</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        <pagination v-if="pagination.last_page > 1"
                                    :pagination="pagination"
                                    :offset="5"
                                    @paginate="query === '' ? getData() : searchData()"
                                    ></pagination>
                    </div>
                </div>
            </div>
        </div>

   <!-- Modal -->
    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="showModalLabel">{{ editMode ? "Edit" : "Add New" }}  Customer</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <form @submit.prevent="editMode ? update() : store()" @keydown="form.onKeydown($event)">
          <div class="modal-body">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" v-model="form.name" name="name" id="name" class="form-control" placeholder="Your Name">
                <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input v-model="form.email"  type="email" name="email" id="email" class="form-control" placeholder="Your Email">
                <div v-if="form.errors.has('email')" v-html="form.errors.get('email')" />
              </div>
              <div class="form-group">
                <label for="phone">Phone</label>
                <input v-model="form.phone"  type="number" name="phone" id="phone" class="form-control" placeholder="Your Phone">
                <div v-if="form.errors.has('phone')" v-html="form.errors.get('phone')" />
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <input v-model="form.address"  type="text" name="address" id="address" class="form-control" placeholder="Your address">
                <div v-if="form.errors.has('address')" v-html="form.errors.get('address')" />
              </div>
              <div class="form-group">
                <label for="total">Total</label>
                <input v-model="form.total"  type="number" name="total" id="total" class="form-control" placeholder="Your total">
                <div v-if="form.errors.has('total')" v-html="form.errors.get('total')" />
              </div>
              <div class="form-group">
                <label for="image">Upload Image</label>
                <input type="file" @change="onFileChange" class="form-control-file" name="image" id="image">
                <div v-if="form.errors.has('image')" v-html="form.errors.get('image')" />
                 <img v-bind:src="showPreview == !null ? imagePreview : '../storage/customer/'+form.image" width="100" height="100"/> 
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button :disabled="form.busy" type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>

        </div>
      </div>
    </div>

    </div>
    
</template>

<script>
import Form from 'vform'
    export default {
        data() {
            return {
                editMode: false,
                query: "",
                queryFiled: "name",
                customers: [],
                imagePreview: null,
                showPreview: false,
                pagination: {
                    current_page:1,
                },
                form: new Form({
                    id: "",
                    name: "",
                    email: "",
                    phone: "",
                    address: "",
                    total: "",
                    image: "",
                }),

            }
        },
        
        watch: {
            query: function(newQ, old) {
            if (newQ === "") {
                this.getData();
            } else {
                console.log(newQ);
                this.searchData();
            }
            }
        },
        mounted() {
            //  [App.vue specific] When App.vue is finish loading finish the progress bar
            this.$Progress.finish()

            console.log('Customer Component mounted.');
            this.getData();
        },
        methods: {
            getData() {
                this.$Progress.start()
                axios.get('/api/customer?page='+this.pagination.current_page)
                    .then(response => {
                        this.customers = response.data.data;
                        this.pagination = response.data.meta;
                        console.log(response)
                        this.$Progress.finish()
                    })
                    .catch(e=> {
                        console.log(e);
                        this.$Progress.fail()
                    })
            },
            searchData() {
            this.$Progress.start();
            axios.get("/api/search/customers/" +
                    this.queryFiled +
                    "/" +
                    this.query +
                    "?page=" +
                    this.pagination.current_page
                )
                .then(response => {
                this.customers = response.data.data;
                this.pagination = response.data.meta;
                this.$Progress.finish();
                })
                .catch(e => {
                console.log(e);
                this.$Progress.fail();
                });
            },
            reload() {
                this.getData();
                this.query = "";
                this.queryFiled = "name";
                this.$snotify.success("Data Successfully Refresh", "Success");
            },   
            create() {
                this.editMode = false;
                this.form.reset();
                this.form.clear();
                $("#showModal").modal("show");
            },
            onFileChange(event){
              this.form.image = event.target.files[0];
            },
            onFileChange(event){
              this.form.image = event.target.files[0];
              let reader  = new FileReader();
              reader.addEventListener("load", function () {
                this.showPreview = true;
                this.imagePreview = reader.result;
              }.bind(this), false);
              if( this.form.image ){
                if ( /\.(jpe?g|png|gif)$/i.test( this.form.image.name ) ) {
                  reader.readAsDataURL( this.form.image );
                }
              }
            },      
            store() {
            this.$Progress.start();
            this.form.busy = true;
            this.form
                .post("/api/customer")
                .then(response => {
                this.getData();
                $("#showModal").modal("hide");
                if (this.form.successful) {
                    this.$Progress.finish();
                    this.$snotify.success("Customer Successfully Saved", "Success");
                } else {
                    this.$Progress.fail();
                    this.$snotify.error(
                    "Something went wrong try again later.",
                    "Error"
                    );
                }
                })
                .catch(e => {
                this.$Progress.fail();
                console.log(e);
                });
            },
            
          edit(customer) {
            this.editMode = true;
            this.form.reset();
            this.form.clear();
            this.form.fill(customer);
            $("#showModal").modal("show");
          },
          update() {
            this.$Progress.start();
            this.form.busy = true;
            this.form
              .post("/api/customer/" + this.form.id)
              .then(response => {
                this.getData();
                $("#showModal").modal("hide");
                if (this.form.successful) {
                  this.$Progress.finish();
                  this.$snotify.success("Customer Successfully Updated", "Success");
                } else {
                  this.$Progress.fail();
                  this.$snotify.error(
                    "Something went wrong try again later.",
                    "Error"
                  );
                }
              })
              .catch(e => {
                this.$Progress.fail();
                console.log(e);
              });
          },


          destroy(customer) {
            this.$snotify.clear();
            this.$snotify.confirm(
              "You will not be able to recover this data!",
              "Are you sure?",
              {
                showProgressBar: false,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                  {
                    text: "Yes",
                    action: toast => {
                      this.$snotify.remove(toast.id);
                      this.$Progress.start();
                      axios
                        .delete("/api/customer/" + customer.id)
                        .then(response => {
                          this.getData();
                          this.$Progress.finish();
                          this.$snotify.success(
                            "Customer Successfully Deleted",
                            "Success"
                          );
                        })
                        .catch(e => {
                          this.$Progress.fail();
                          console.log(e);
                        });
                    },
                    bold: true
                  },
                  {
                    text: "No",
                    action: toast => {
                      this.$snotify.remove(toast.id);
                    },
                    bold: true
                  }
                ]
              }
            );
          }


        },
    }

</script>
