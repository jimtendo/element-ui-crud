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
          endpoint: String,
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
            
            var self = this;
            this.$http.delete(this.endpoint).then(function(response) {
              self.$notify.success( {title: 'Success', message: 'Item has been deleted.' });
              self.loading = false;
              
              // Perform callback
              if (self.after) {
                  self.after();
              }
              
            }).catch(function(error) {
                self.$notify.error( {title: 'Error', message: error.response.data.message });
                self.loading = false;
            });
          },
        }
    }
</script>
