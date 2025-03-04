<script setup>
import Header from '../pages/Layout/Header.vue';
import Footer from '../pages/Layout/Footer.vue';
</script>

<template>
  <div>
    <Header />
    <main>
      <slot></slot>
    </main>
    <Footer />
  </div>
</template>
