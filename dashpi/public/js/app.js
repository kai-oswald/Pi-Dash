/* global Vue */
/* global notie */
/* global $ */
var product = Vue.component("product", {
  template: "#product",
  data: function() {
    return {
      product: new Product()
    };
  },
  methods: {
    saveProduct: function() {
      var url = "/api/products";
      this.$http.post(url, this.product).then(response => {
      this.product = new Product();
      $('#productModal').modal('hide');
      notie.alert("success", "Success.", 1.5);
      // TODO: add event and automatically update parent
      this.$emit("save-product", response.body);
      
    }, response => {
    // error callback
    console.log(response);
    notie.alert('error', response.statusText, 1.5);
    });
    },
  }
});

var products = Vue.component("products", {
  template: "#products",
  data: function() {
    return {
      products: [],
    };
  },
  methods: {
    newProduct: function() {
      $('#productModal').modal();
    },
    updateProducts: function(prod) {
      this.products.push(prod);
    }
  },
  created: function() {
      var url = "/api/products";
      this.$http.get(url).then(response => {
      this.products = response.body;
    }, response => {
    // error callback
    notie.alert('error', response.statusText, 1.5);
    });
  },
});

// ---------------------------
//            CART
// ---------------------------
var cart = Vue.component("cart", {
 template: "#cart",
  data: function()  {
      return {
    cart: []
      };
  },
  methods: {
    saveCart: function() {
        // TODO
        // POST to api/cart/
        // this.cart ist nicht ganz in der Form in der wir es an die API schicken wollen.
        var url = "/api/cart";
        this.$http.post(url, this.cart).then(response => {
          console.log(response.body);
        }, response => {
          // error callback
          notie.alert("error", response.statusText, 1.5);
        });
      }
  },
  created: function() {
      var url = "/api/cart";
      this.$http.get(url).then(response => {
      this.cart = response.body;
  }, response => {
  // error callback
  notie.alert("error", response.statusText, 1.5);
  });
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
    return "€ " + value.toFixed(2);
  }
  return 0;
});

new Vue({
  el: '#main'
});


// MODELS
function Product() {
  this.name = "",
  this.price = 0
}