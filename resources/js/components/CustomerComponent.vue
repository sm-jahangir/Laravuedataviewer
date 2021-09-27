<template>
    <div class="container">
        
        <!-- set progressbar -->
        <vue-progress-bar></vue-progress-bar>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Customer Component</div>

                    <div class="card-body">
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
                                <tr v-for="(customer, index) in customers" :key="customer.id">
                                    <th scope="row">{{index+1}}</th>
                                    <td>{{customer.name}}</td>
                                    <td>{{customer.email}}</td>
                                    <td>{{customer.phone}}</td>
                                    <td>{{customer.total}}</td>
                                    <td>
                                        <a href="">Deleted</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <pagination v-if="pagination.last_page > 1"
                                    :pagination="pagination"
                                    :offset="5"
                                    @paginate="getData()"
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
                customers: [],
                pagination: {
                    current_page:1,
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
                axios.get('api/customer?page='+this.pagination.current_page)
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
            }
        },
    }

</script>
