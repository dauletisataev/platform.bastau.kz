<template>
    <div class="col-10 offset-2">
        <p>
          {{ $tc("dashboard.hello") }}
          {{ $tc("dashboard.name") }}
          {{ $tc("dashboard.surname") }}
        </p>
    </div>
</template> 

<script>
export default {
  props:['user'],
  data() {
    return {};
  },
  methods: {},

};
</script>
