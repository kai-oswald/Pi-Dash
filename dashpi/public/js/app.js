
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
      },
      saveCart: function() {
        // TODO
        // POST to api/cart/
        // this.cart ist nicht ganz in der Form in der wir es an die API schicken wollen.
      }
  },
  computed: {
    total: function() {
      // TODO: update on propchanged
      var total = 0;
      if(this.cart !== undefined) {
      for(var i = 0; i < this.cart.length; i++) {
        total += this.cart[i].quantity * this.cart[i].price;
      }
      }
      return total;
    }
  }
});

Vue.filter("currency", function(value) {
  if(value!== null) {
    return value.toFixed(2) + " €";
  }
  return 0;
});

new Vue({
  el: '#main'
})
