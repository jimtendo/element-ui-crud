<template>
    <div v-loading="loading" :element-loading-text="loadingText" loading-custom-class="test" style="min-height:100px;">
        <el-form v-if="entity" ref="form" :model="entity" :rules="rules" label-width="150px">
            <div v-for="column in columns" :key="column">
                <slot :name="column" :entity="entity">
                    <el-crud-field :name="column" :fields="fields" :titles="titles" v-model="entity[column]"></el-crud-field>
                </slot>
            </div>
            
            <div style="margin-top:1em; text-align:right;">
                <el-button type="primary" v-on:click="submitForm()">Update</el-button>
            </div>
        </el-form>
    </div>
</template>

<script>
    import ElCrudField from './Field.vue'
    import Helpers from './Helpers.vue'
    
    export default {
        mixins: [ Helpers ],
        components: { ElCrudField },
      
        props: {
          endpoint: [String, Function], 
          entity: { type: Object, default: null },
          columns: Array,
          titles: { type: Object, default: () => { return {}; } },
          rules: Object,
          params: { type: Object, default: () => { return {}; } },
          fields: { type: Object, default: () => { return {}; } },
          after: { type: Function, default: null },
        },
        
        data () {
          return {
            loading: false,
            loadingText: 'Loading...',
            loadingClass: '',
            entity: null,
          }
        },
        
        created() {
            this.fetchData();
        },
              
        methods: {
          
          /**
            * Fetch data from the server
            */
          fetchData: function() {
            this.loading = true;
            
            var self = this;
            
            var handleSuccess = function(response) {
              self.entity = response;
              self.loading = false;
            }
            
            var handleError = function(error) {
              console.log(error);
              self.loadingText = error.data.message;
            }
            
            // If endpoint is a string...
            if (typeof this.endpoint === 'string') {
              this.$http.get(this.endpoint+'/edit', { 'params': self.params }).then(handleSuccess).catch(handleError);
            } else { // Otherwise it is a function...
              this.endpoint('fetch', self.entity, self).then(handleSuccess).catch(handleError);
            }
            
            /*
            this.$http.get(this.endpoint+'/edit').then(function(response) {
                self.entity = Object.assign({}, response.data.entity);
                self.loading = false;
            }).catch(function(error) {
                self.loadingText = error.response.data.message;
                self.loadingClass = 'error';
                self.loading = false;
            });*/
          },
          
          /**
            * Submit the form (create entity)
            */
          submitForm: function(callback) {
            // Validate form
            this.$refs['form'].validate((valid) => {
                if (valid) {
                    var self = this;
                    this.loading = true;
                    
                    var handleSuccess = function(response) {
                        self.$notify.success( {title: 'Success', message: response.data.message });
                        self.loading = false;
                        
                        // Perform callback
                        if (self.after) {
                            self.after(self.entity);
                        }
                    }
                    
                    var handleError = function(error) {
                        self.$notify.error( {title: 'Error', message: error.response.data.message });
                        self.loading = false;
                    }
                    
                    // If endpoint is a string...
                    if (typeof this.endpoint === 'string') {
                      this.$http.put(this.endpoint+'/edit', this.entity, { 'params': this.params }).then(handleSuccess).catch(handleError);
                    } else { // Otherwise it is a function...
                      this.endpoint('submit', self.entity, self).then(handleSuccess).catch(handleError);
                    }
                } else {
                  this.$notify.error( {title: 'Cannot submit', message: 'Form contains errors.' });
                  return false;
                }
            });
          },
        }
    }
</script>
