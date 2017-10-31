<template>
    <div>
        <el-table v-loading="loading" :element-loading-text="loadingText" :data="rows" style="width: 100%"
                  v-on:sort-change="handleSortChange" v-on:selection-change="handleSelectionChange" :row-class-name="rowClassName">
          <el-table-column v-if="selectable" type="selection" width="55"></el-table-column>
          <el-table-column v-for="name in columns" :key="name" :prop="name" :label="(titles.hasOwnProperty(name)) ? titles[name] : name" sortable="custom">
            <template slot-scope="scope">
              <slot :name="name" :row="scope.row">{{ fromDotNotation(scope.row, name) }}</slot>
            </template>
          </el-table-column>
          <el-table-column label="Actions" header-align="center" align="right" width="196px">
            <template slot-scope="scope">
              <el-button-group>
                <slot :row="scope.row"></slot>
              </el-button-group>
            </template>
          </el-table-column>
        </el-table>

        <el-pagination style="margin-top:1em;" v-if="pagination"
          @current-change="handleCurrentChange"
          :current-page.sync="activePage"
          :page-size="pagination"
          layout="total, prev, pager, next"
          :total="total">
        </el-pagination>
    </div>
</template>

<script>
    import Helpers from './Helpers.vue'
    
    export default {
        mixins: [ Helpers ],
        
        props: {
          endpoint: String,
          primaryKey: { type: String, default: 'id' },
          columns: Array,
          titles: { type: Object, default: () => { return {}; } },
          order: { type: Object, default: () => {} },
          params: { type: Object, default: () => {} },
          pagination: { type: Number, default: 0 },
          selectable: { type: Boolean, default: false },
          rowClassName: { type:Function, default: null },
        },
        
        data () {
          return {
            loading: false,
            loadingText: 'Loading...',
            loadingClass: '',
            rows: [],
            total: 0,
            activePage: 1,
            selected: [],
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
            this.loadingText = 'Loading...';
            
            // Compile parameters
            var params = { };
            if (this.order) {
                params = Object.assign({}, params, {'order': this.order});
            }
            
            if (this.pagination) {
                params = Object.assign({}, params, {'limit':this.pagination, 'offset': this.pagination*(this.activePage-1)});
            }
            
            if (typeof this.params != 'undefined') {
                params = Object.assign({}, params, this.params);
            }
            
            var self = this;
            this.$http.get(this.endpoint, { 'params': params }).then(function(response) {
                self.total = response.data.total;
                self.rows = response.data.rows;
                self.loading = false;
            }).catch(function(error) {
                self.loadingText = error.response.data.message;
            });
          },
          
          /**
            * Handle Pagination Changed
            */
          handleCurrentChange: function() {
            this.fetchData();
          },
          
          /**
            * Handle Sorting Change
            */
          handleSortChange: function(column, prop, order) {
            console.log(column);
            console.log(prop);
            console.log(order);
          },
          
          /**
            * Handle selection change
            */
          handleSelectionChange: function(selected) {
              this.selected = selected;
          },
        },
    }
</script>

<style scoped>
.table thead tr th { text-align: center; }
.table tbody tr td { vertical-align: middle; }
.table tbody tr td:last-child { width:1%; white-space:nowrap; }
</style>
