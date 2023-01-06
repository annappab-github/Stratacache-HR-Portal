<template>
<div class="form-group">
    <label :for="inputId">{{ inputLable }}</label>
    <input class="form-control" :id="inputId" :type="inputType" :value="enteredValue" :placeholder="inputPlaceholder" :readonly="readOnly">
</div>
</template>

<script>
export default {
  name: 'Input',
  props: ['input-lable', 'input-id', 'entered-value', 'input-type', 'input-placeholder', 'read-only'],
  mounted() {
    
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>