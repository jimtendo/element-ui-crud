<script>
export default {
    methods: {
      fromDotNotation: function(obj, path) {
        var i = 0,
        path = path.split('.');

        for (; i < path.length; i++) {
            if (typeof obj[path[i]] === 'undefined' || !obj[path[i]]) {
              return ''; 
            }
            
            obj = obj[path[i]];
        }

        return obj;
      },
    }
}
</script>
