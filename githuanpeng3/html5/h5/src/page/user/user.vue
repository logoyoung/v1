<template>
  <div>
    <!--<v-header>-->
      <!--<p slot="title" class="title">我的</p>-->
    <!--</v-header>-->
    <main-nav></main-nav>
  </div>
</template>

<script>
//import vHeader from '@/components/header/header';
import mainNav from '@/components/nav/main-nav';
export default {
  name: 'user',
  components: { mainNav }
}
</script>

<style>

</style>
