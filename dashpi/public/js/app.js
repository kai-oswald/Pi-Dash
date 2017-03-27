/* global Vue */
/* global notie */
/* global $ */

// ---------------------------
//         Api-Routes
// ---------------------------
var api = {
  products: "/api/products/",
  productbuttons: "/api/productbuttons/",
  sender: "/api/sender/",
  cart: "/api/cart/",
  config: "/api/config/",
  status: "/api/server/"
}

// ---------------------------
//       Vue Components
// ---------------------------

// ---------------------------
//        StatusButton
// ---------------------------
var statusBtn = Vue.component("statusBtn", {
  template: "#statusBtn",
  props: ["status", "type"],
  data: function() {
    return {
      currentStatus: this.status
    }
  },
  methods: {
    startServer: function() {
      var url = api.status + this.type + "/start";
      this.$http.get(url).then(response => {
        this.currentStatus = response.body.status;
    }, response => {
      notie.alert({
        type: "error",
        text: response.statusText,
        time: 1.5
      });
    });
    }
  }
});

var status = Vue.component("status", {
  template: "#status",
  data: function() {
    return {
      status: {}
    }
  },
  created: function() {
    var url = api.status + "status";
    this.$http.get(url).then(response => {
      this.status = response.body;
    }, response => {
      notie.alert({
        type: "error",
        text: response.statusText,
        time: 1.5
      });
    });
    // TEST
    /*this.status = {
      "udp": true,
      "tcp": false
    }*/
  }
});

// ---------------------------
//      Product Dropdown
// ---------------------------
var productDrop = Vue.component("productdrop", {
  template: "#productDrop",
  props: ["senderid", "productname", "auto"],
  data: function () {
    return {
      products: [],
      currentDescription: this.productname,
      currentSender: this.senderid,
    }
  },
  created: function () {
    var url = api.products;

    this.$http.get(url).then(response => {
      this.products = response.body;
    }, response => {
      notie.alert({
        type: "error",
        text: response.statusText,
        time: 1.5
      });
    });
  },
  methods: {
    updateSender: function (product) {
      this.currentDescription = product.name;
      if(this.auto) {
      var url = api.productbuttons;
      var data = {
        productid: product.id,
        senderid: this.currentSender,
      }

      this.$http.post(url, data).then(response => {
        notie.alert({
          type: "success",
          text: "succesfully updated sender " + this.currentSender,
          time: 1.5
        });
        this.$emit("update-sender", response.body);
      }, response => {
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        });
      });
      } else {
        this.$emit("update-product", product);
      }
    }
  }
});

// ---------------------------
//            Senders
// ---------------------------
var sender = Vue.component("sender", {
  template: "#sender",
  data: function() {
    return {
      sender: new Sender()
    }
  },
  methods: {
   saveSender: function(sender) {
     var url = api.config;
     var data = sender;
     this.$http.post(url, data).then(response => {
        notie.alert({
          type: "success",
          text: "succesfully added a new sender.",
          time: 1.5
        });
        this.$emit("update-senders", response.body);
        window.location.reload(); // TODO: ugly workaround
      }, response => {
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        });
      });
   },
   updateSenders: function() {
     
   },
   
   updateProduct: function(product) {
     this.sender.productid = product.id;
     this.sender.name = product.name;
   }
  }
});

var senders = Vue.component("senders", {
  template: "#senders",
  data: function () {
    return {
      config: []
    }
  },
  created: function () {
    var url = api.config;
    this.$http.get(url).then(response => {
      this.config = response.body;
    }, response => {
      notie.alert({
        type: "error",
        text: response.statusText,
        time: 1.5
      })
    });
  },
  methods: {
    update: function (sender) {
      var url = api.sender;
      var data = {
        id: sender.id,
        comment: sender.comment
      }
      this.$http.put(url, data).then(response => {
        var msg = "updated comment for sender " + response.body.id + " to " + response.body.comment
        notie.alert({
          type: "success",
          text: msg,
          time: 1.5
        })
      }, response => {
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        })
      });
    },
    updateSenders: function () {
      var url = api.config;
      this.$http.get(url).then(response => {
        this.config = response.body;
      }, response => {
        // error callback
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        })
      });
    }
  }
});

// ---------------------------
//            Product
// ---------------------------
var product = Vue.component("product", {
  template: "#product",
  data: function () {
    return {
      product: new Product()
    };
  },
  methods: {
    saveProduct: function () {
      var url = api.products;
      this.$http.post(url, this.product).then(response => {
        this.product = new Product();
        $('#productModal').modal('hide');
        notie.alert({
          type: "success",
          text: "success",
          time: 1.5
        });
        this.$emit("save-product", response.body);
      }, response => {
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        });
      });
    }
  }
});

// ---------------------------
//            Products
// ---------------------------
var products = Vue.component("products", {
  template: "#products",
  data: function () {
    return {
      products: [],
      currentProduct: {}
    };
  },
  methods: {
    newProduct: function () {
      $('#productModal').modal();
    },
    updateProducts: function (prod) {
      this.products.push(prod);
    },
    editProduct: function (product) {

    },
    deleteProduct: function (product) {
      var url = api.products + product.id;
      this.$http.delete(url).then(response => {
        var indexToRemove;
        this.products.forEach(function (currProd, index) {
          if (currProd.id === product.id) {
            indexToRemove = index;
          }
        });
        this.products.splice(indexToRemove, 1);
        notie.alert({
          type: "success",
          text: "successfully deleted product '" + product.name + "'",
          time: 2
        });
      }, response => {
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        });
      });
    },
    updateProduct: function (product) {
      if (this.currentProduct.name !== product.name || this.currentProduct.price !== product.price) {
        // PUT
        var url = api.products + product.id;
        this.$http.put(url, product).then(response => {
          // user feedback?
        }, response => {
          // error callback
          notie.alert({
            type: "error",
            text: response.statusText,
            time: 1.5
          });
        });
      }
    },
    getProduct: function (product) {
      this.currentProduct.name = product.name;
      this.currentProduct.price = product.price;
    }
  },
  created: function () {
    var url = api.products;
    console.log("getting products...");
    this.$http.get(url).then(response => {
      this.products = response.body;
      console.log(response.body);
    }, response => {
      console.log("error");
      notie.alert({
        type: "error",
        text: response.statusText,
        time: 1.5
      });
    });
  },
});

// ---------------------------
//            Cart
// ---------------------------
var cart = Vue.component("cart", {
  template: "#cart",
  data: function () {
    return {
      cart: []
    };
  },
  methods: {
    saveCart: function () {
      var url = api.cart;
      for (var i = 0; i < this.cart.length; i++) {
        var cartItem = this.cart[i];
        if (cartItem.quantity === 0) {
          this.$http.delete(url + cartItem.productid).then(response => {}, response => {
            notie.alert({
              type: "error",
              text: response.statusText,
              time: 1.5
            });
          });
        }
      }
      this.$http.post(url, this.cart).then(response => {
        notie.alert({
          type: "success",
          text: "success",
          time: 1.5
        });
      }, response => {
        notie.alert({
          type: "error",
          text: response.statusText,
          time: 1.5
        });
      });
    }
  },
  created: function () {
    var url = api.cart;
    this.$http.get(url).then(response => {
      this.cart = response.body;
    }, response => {
      notie.alert({
        type: "error",
        text: response.statusText,
        time: 1.5
      });
    });
  },
  computed: {
    total: function () {
      var total = 0;
      if (this.cart !== undefined) {
        for (var i = 0; i < this.cart.length; i++) {
          total += this.cart[i].quantity * this.cart[i].price;
        }
      }
      return total;
    }
  }
});

// ---------------------------
//            Filters
// ---------------------------
Vue.filter("currency", function (value) {
  if (value !== null) {
    return "€ " + value.toFixed(2);
  }
  return 0;
});

Vue.filter("uppercase", function(text) {
  return text.toUpperCase();
});

// ---------------------------
//            Vue Instance
// ---------------------------
new Vue({
  el: '#main'
});


// ---------------------------
//            Models
// ---------------------------
function Product() {
  this.name = "",
    this.price = 0
}

function Sender() {
  this.comment = "";
  this.productid = -1;
  this.name = "";
}
