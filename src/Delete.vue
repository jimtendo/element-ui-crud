<template>
    <div v-loading="loading">
        <el-form ref="form" label-width="120px">
            <p>Are you sure you wish to delete this entry?</p>
            
            <div style="margin-top:1em; text-align:right;">
                <el-button type="primary" v-on:click="submitForm()">Confirm</el-button>
            </div>
        </el-form>
    </div>
</template>

<script>
    export default {
      
        props: {
          endpoint: [String, Function],
          entity: { type: Object, default: null },
          after: { type: Function, default: null },
        },
        
        data () {
          return {
            loading: false,
          }
        },
        
        methods: {
          
          /**
            * Submit the form (create entity)
            */
          submitForm: function(callback) {

            this.loading = true;
            
            // If endpoint is a string...
            var self = this;
            if (typeof this.endpoint === 'string') {
              this.$http.delete(this.endpoint)
                        .then(this.handleSuccess)
                        .catch(this.handleError);
            } else { // Otherwise it is a function
              this.endpoint(this.entity, self)
                  .then(this.handleSuccess)
                  .catch(this.handleError);
            }
          },
          
          handleSuccess: function(response) {
            this.$notify.success( {title: 'Success', message: 'Item has been deleted.' });
            this.loading = false;
            
            // Perform callback
            if (this.after) {
                this.after();
            }
          },
          
          handleError: function(error) {
            console.log(error);
            this.$notify.error( {title: 'Error', message: error.response.data.message });
            this.loading = false;
          },
        }
    }
</script>
