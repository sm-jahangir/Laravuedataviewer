<template>
    <div class="container">
        
        <!-- set progressbar -->
        <vue-progress-bar></vue-progress-bar>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    
            <div class="card-header">
                <h4 class="card-title">Customers</h4>
                <div class="card-tools" style="position: absolute;right: 1rem;top: .5rem;">
                <button type="button" class="btn btn-info">
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
                                    <td>{{customer.name}}</td>
                                    <td>{{customer.email}}</td>
                                    <td>{{customer.phone}}</td>
                                    <td>{{customer.total}}</td>
                                    <td>
                                        <a href="">Deleted</a>
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
    </div>
    
</template>

<script>
    export default {
        data() {
            return {
                query: "",
                queryFiled: "name",
                customers: [],
                pagination: {
                    current_page:1,
                }
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
            },


        },
    }

</script>
