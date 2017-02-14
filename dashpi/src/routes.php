<?php
$APP_ROOT="/slim/Pi-Dash/dashpi/public";

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
    return $this->renderer->render($res, "cart.phtml", $args);
});
$app->get("/config", function($req, $res, $args) {
    // show current configuration
    $sender = \Sender::all();
    return $this->renderer->render($res, "config.phtml", array("sender"=>$sender->toArray()));
});
$app->get("/home", function($req, $res, $args) {
    // show current configuration
    return $this->renderer->render($res, "home.phtml", $args);
});

// REST API
$app->group("/api", function() use ($app) {
    
    
    // products
    // get all products    
    $app->get("/products", function($req, $res, $args) {
        // $products = $this->db->table("Products")->get();
        return $res->withJson(\Product::all());    
    });
    
    //get product by id
    $app->get("/products/{id}", function($req, $res, $args){
        $product = \Product::find($args["id"]);
        if(sizeof($product) == 0) {            
            return $res->withJson(CreateErrorMessage($args["id"]), 404);
        }     
        return $res->withJson($product);
    });  
    
    // Content-Type: application/json must be set to succesfully read body content!
    // add product
    $app->post("/products", function($req, $res, $args) {
        // validate
        $request = $req->getParsedBody();
        try {
            $product = new Product;
            $product->name = $request["name"];
            $product->price = $request["price"];
            $product->save();
            return $res->withJson($product);        
        }
        catch(Exception $e) {
            return $res->withJson($e, 400);
        }
    });
    // update product id
    $app->put("/products/{id}", function($req, $res, $args) {
        try {
            $product = \Product::find($args["id"]);
            $product->name = $args["name"];
            $product->price = $args["price"];
            $product->save();
            return $res->withJson($product);
        }
         catch(Exception $e) {
            return $res->withJson($e, 400);
        }
    }); 
    // delete product by id
    $app->delete("/products/{id}", function($req, $res, $args) {
        try {
            $product = \Product::find($args["id"]);
            $product->delete();
            return $res->withJson($args[id]);
        }
        catch(Exception $e) {
            return $res->withJson($e, 400);
        }
    });  
    
    // cart: current orders
    $app->get("/cart", function($req, $res, $args) {
        // construct the cart (all open orders)
        $cart = \Cart::all();
        $currentcart = array();
        //$product = \Product::find($cart->productid);
        $counter = 0;
        foreach($cart as $item) {
            $product = \Product::find($item->productid);
            $current = null;
            $current->name = $product->name;
            $current->price = $product->price;
            $current->quantity = $item->quantity;
            array_push($currentcart, $current);
        }
        // TODO: get products and return full cart:
        // item.name -> get name from cart.productid
        // item.price -> get price form cart.productid
        // cart.quantity
        return $res->withJson($currentcart); 
    });
    
    $app->post("/cart/{senderid}", function($req, $res, $args) {
        try {
            // first load product of sender
            $column = 'senderid';
            $productsender = \Productbutton::where($column, '=', $args["senderid"])->first();
            if($productsender == "null" || $productsender == null)
            {
               // TODO: error message 
            }
            else
            {
                $productid = $productsender->productid;
                // update cart
                $currentcart = \Cart::where('productid', '=',$productid)->first();
                if($currentcart == "null" || $currentcart == null)
                {
                    $cart = new Cart;
                    $cart->productid= $productid;  
                    $cart->quantity=1;
                    $cart->save();
                    return $res->withJson($cart);
                }
                else 
                {
                    $currentcart->quantity++;
                    $currentcart->productid= $productid;  
                    $currentcart->save();
                    return $res->withJson($currentcart);
                }
                
            }
        }
        catch(Exception $e) {
            return $res->withJson($e, 400);
        }
    });  

    $app->get("/sender", function($req, $res, $args) {
        // construct the cart (all open orders)
        $sender = \Sender::all();
        return $res->withJson($sender); 
    });
    
    $app->post("/sender", function($req, $res, $args) {
        // construct the cart (all open orders)
        try {
            $request2 = $req->getParsedBody();
            $sender = new Sender;
            $sender->comment = $request2["comment"];
            $sender->save();
            // TODO createSkript: rigth Skript for Arduino and give it back
            return $res->withJson($sender);
        }
        catch(Exception $e) {
            return $res->withJson($e, 400);
        }
        return $res->withJson($sender); 
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


function createSkript($id){
    return "example".$id."text";
}