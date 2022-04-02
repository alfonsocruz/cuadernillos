<template>
  <button
    type="button"
    data-toggle="modal"
    data-target="#exampleModal"
    :class="classes"
    @click="click(data)"
    title="Upload"
  >
    <span>
      <i class="voyager-upload" aria-hidden="true"></i>
    </span>
    &nbsp;
    {{ name }}
  </button>
</template>

<script>
export default {
  props: {
    data: {},
    name: {},
    click: {
      type: Function,
      default: () => {},
    },
    meta: {},
    classes: {
      type: Object,
      default: () => ({
        btn: true,
        "btn-primary": true,
        "btn-sm": true,
      }),
    },
  },
};
</script>