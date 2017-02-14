
    var cart = Vue.component("cart", {
 template: "#cart",
  data: function()  {
      return {
    cart: this.getCart(),
      }
  },
  methods: {
      getCart: function() {
        var url = "https://pidash-kaos1910.c9users.io/api/cart";
        this.$http.get(url).then(response => {
        this.cart = response.body;
        console.log(this.cart);
    }, response => {
    // error callback
    });
      }
  }
});

new Vue({
  el: '#main',
})
