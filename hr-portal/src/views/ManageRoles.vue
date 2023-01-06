<template>
    <div>
        <ManageRole :direction="direction" :sort="sort" @updateSort="updateSort" />
    </div>
</template>

<script>
import ManageRole from '../components/ManageRole.vue';

export default {

  data() {
    return {
      sort: '',
      direction: 'ascending', // 'ascending' || 'descending'
    };
  },
  components: {
    ManageRole,
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
