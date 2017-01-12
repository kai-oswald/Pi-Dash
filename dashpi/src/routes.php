<?php

// Custom functions
function CreateErrorMessage($id) {
    return array("error" => "no entry with id = ".$id.".");
}
function ValidateProduct($product) {    
    return $product != NULL;
}
// Routes
$app->get('/', function ($req, $res, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($res, 'index.phtml', $args);
});

$app->get("/cart", function($req, $res, $args) {
    // get cart and show on frontend
    return $res->write("Your cart:");
});
$app->get("/config", function($req, $res, $args) {
    // show current configuration
    return $res->write("Configuration");
});
$app->post("/config", function($req, $res, $args) {
    // update configuration
    return $res->write("Configuration");
});
// REST API
$app->group("/api", function() use ($app) {

    // products
    $app->get("/products", function($req, $res, $args) {
        $products = $this->db->table("Products")->get();
        return $res->withJson($products);    
    });
    $app->get("/products/{id}", function($req, $res, $args){
        $product = $this->db->table("Products")->find($args["id"]);
        if($product == NULL) {            
            return $res->withJson(CreateErrorMessage($args["id"]));
        }     
        return $res->withJson($product);
    });  
    $app->post("/products", function($req, $res, $args) {
        // validate
        $product = $args["product"];
        ValidateProduct($product);

        // add new product
    });
    $app->put("/products/{id}", function($req, $res, $args) {
        // validate

        // update exisiting product with this id
    }); 
    $app->delete("/products/{id}", function($req, $res, $args) {
        // validate

        // delete exisiting product with this id
    });  
    // cart
    $app->get("/cart", function($req, $res, $args) {
        // construct the cart (all open orders)
        $cart = $this->db->table("Cart")->get();
        return $res->withJson($cart); 
    });

    // orders (product + count + open/closed)
    $app->get("/orders", function($req, $res, $args) {
        // get all orders
        $orders = $this->db->table("Orders")->get();
        return $res->withJson($orders); 
    });
    $app->get("/orders/{id}", function($req, $res, $args) {
        // get order with this id
    });
    $app->post("/orders", function($req, $res, $args) {
        // add new order
    });
    $app->put("/orders/{id}", function($req, $res, $args) {
        // update order with this id
    });
    $app->delete("/orders/{id}", function($req, $res, $args) {
        // delete order with this id
    });


    // config
    // TODO: allow only one config?
    $app->get("/config", function($req, $res, $args) {
        // get current config
    });
    $app->post("/config", function($req, $res, $args) {
        // update config
    });
});
