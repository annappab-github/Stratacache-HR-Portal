<template>
<div>
    <ManageUser :direction="direction" :sort="sort" @updateSort="updateSort" />
</div>
    
</template>

<script>
import ManageUser from '../components/ManageUser.vue';

export default {
  data() {
    return {
      sort: '',
      direction: 'ascending', // 'ascending' || 'descending'
    };
  },
  components: {
    ManageUser
  },
  methods: {
    updateSort(value) {
      if(this.sort == value) {
        this.direction = this.direction == 'ascending' ? 'descending' : 'ascending';
      }else {
        this.sort = value;
        this.direction = 'ascending';
      }
    },
  },
};
</script>
