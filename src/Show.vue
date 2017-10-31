<template>
    <div>
        <div v-if="error" class="alert alert-danger">{{ error['message'] }}</div>
    
        <div v-for="(value, column) in data['entity']" class="field">
            <div class="title">{{ column }}</div>
            <div class="value">{{ value }}</div>
            <div class="hint">{{ column }}</div>
        </div>
        
        <div class="buttons">
            <div v-for="button in data['buttons']">
                <a class="btn btn-default btn-sm" v-on:click="button['click']"><i :class="button['icon']" :title="button['title']"></i> {{ button['title'] }}</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
      
        props: ['endpoint'],
        
        data () {
          return {
            data: [],
            error: null,
          }
        },
        
        created() {
          this.fetchData();
        },
              
        methods: {
          
          /**
           * Fetch data from the server
           */
          fetchData() {
            this.error = null;
            
            this.$http.get(this.endpoint).then(response => {
                this.data = response.data;
            }, response => {
                this.error = response.data;
            });
          },
          
          getTitle(column) {
            if ('titles' in this.data && column in this.data['titles']) {
              return this.data['titles'][column];
            }
            
            return column;
          },
          getHint(column) {
            return 'This is a hint.';
          }
        }
    }
</script>

<style>
  div.field { margin-bottom:1em; }
  div.title { border-bottom:1px solid #000; }
  div.hint { color:#aaa; }
</style>
