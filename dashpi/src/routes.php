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

$app->get("/products", function($req, $res, $args) {
    // show current configuration
    return $this->renderer->render($res, "products.phtml", $args);
});

// REST API
$app->group("/api", function() use ($app) {
    
    
    // products
    // get all products    
    $app->get("/products/", function($req, $res, $args) {
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
    $app->post("/products/", function($req, $res, $args) {
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
          $request = $req->getParsedBody();
        try {
            $product = \Product::find($args["id"]);
            $product->name = $request["name"];
            $product->price = $request["price"];
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
    $app->get("/cart/", function($req, $res, $args) {
        // construct the cart (all open orders)
        // TODO: this funcitonalit with SQL join?
        $cart = \Cart::all();
        $currentcart = array();
        //$product = \Product::find($cart->productid);
        $counter = 0;
        foreach($cart as $item) {
            $product = \Product::find($item->productid);
            $current = null;
            $current->productid = $item->productid;
            $current->name = $product->name;
            $current->price = $product->price;
            $current->quantity = $item->quantity;
            array_push($currentcart, $current);
        }
        return $res->withJson($currentcart); 
    });
    $app->delete("/cart/{productid}", function($req, $res, $args) {
        $request = $req->getParsedBody();
        try {
            $cart = Cart::where("productid", "=", $args["productid"])->first();
            if(sizeof($cart) != 0) {
                $cart->delete($args["productid"]);
            }
            else {
                $msg = "no product with id " . $args["productid"] ." in cart.";
                return $res->withJson($msg, 404);
            }
        }
        catch(Exception $e) {
            return $res->withJson($e, 400);
        }
    });
    $app->post("/cart/", function($req, $res, $args) {
            $request = $req->getParsedBody();
            $fullCart = array();
            foreach($request as $cartItem) {
                try {
                    $cart = \Cart::where("productid", "=", $cartItem["productid"])->first();
                    if(sizeof($cart) == 0) {
                        $cart = new Cart;
                    }
                    $cart->productid = $cartItem["productid"];
                    $cart->quantity = $cartItem["quantity"];
                    $cart->save();
                    array_push($fullCart, $cart);
                }
                catch(Exception $e) {
                    return $res->withJson($e, 400);
                }
            }
            return $res->withJson($fullCart); 
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

    $app->get("/sender/", function($req, $res, $args) {
        // construct the cart (all open orders)
        $sender = \Sender::all();
        return $res->withJson($sender); 
    });
    $app->put("/sender/", function($req, $res, $args) {
        $request = $req->getParsedBody();
        try {
            $sender = Sender::find($request["id"]);
            if(sizeof($sender) != 0) {
                $sender->comment = $request["comment"];
                $sender->save();
                return $res->withJson($sender);
            } else {
                $msg = "Sender with id " . $request["id"]. " not found.";
                return $res->withJson($msg, 404);
            }
            } 
            catch(Exception $e) {
                return $res->withJson($e, 400);
        }
    });
    $app->post("/sender/", function($req, $res, $args) {
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
    $app->get("/orders/", function($req, $res, $args) {
        // get all orders
        $orders = $this->db->table("Orders")->get();
        return $res->withJson($orders); 
    });
    $app->get("/orders/{id}", function($req, $res, $args) {
        // get order with this id
    });
    $app->post("/orders/", function($req, $res, $args) {
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
    $app->get("/config/", function($req, $res, $args) {
        // get current config
        $senders = Sender::all();
        $vm = array();
        foreach($senders as $sender) {
            $productbutton = Productbutton::where("senderid", "=", $sender->id)->first();
            if(sizeof($productbutton) != 0) {
                $product = Product::find($productbutton->productid);
                if(sizeof($product) != 0) {
                    $viewmodel->id = $sender->id;
                    $viewmodel->comment = $sender->comment;
                    $viewmodel->productid = $product->id;
                    $viewmodel->productname = $product->name;
                    array_push($vm, $viewmodel);
                }
            }
        }
        return $res->withJson($vm);
        
    });
    $app->post("/config/", function($req, $res, $args) {
        // update config
        $request = $req->getParsedBody();
        $senderid = $request["id"];
        $comment = $request["comment"];
        $productid = $request["productid"];
        $productname = $request["name"];
        try {
            $sender = Sender::find($senderid);
            if(sizeof($sender) == 0) {
                $sender = new Sender;
            }
            $sender->comment = $comment;
            $sender->save();
            
            $productbutton = Productbutton::where("senderid", "=", $senderid)->first();
            if(sizeof($productbutton) == 0) {
                $productbutton = new Productbutton;
            }
            $productbutton->senderid = $senderid;
            $productbutton->productid = $productid;
            $productbutton->save();
        }
        catch(Exception $e) {
            return $res->withJson($e, 400);
        }
        
        
        
    });
    $app->get("/productbuttons/", function($req, $res, $args) {
        $productbuttons = Productbutton::all();
        return $res->withJson($productbuttons); 
    });
    $app->post("/productbuttons/", function($req, $res, $args) {
        $request = $req->getParsedBody();
        $senderid = $request["senderid"];
        $productid = $request["productid"];
        $productbutton = Productbutton::where("senderid", $senderid)->first();
        if($productbutton != NULL) {
            $productbutton->productid = $productid;
            $productbutton->save();
        }
        else {
            // Sender doesn't have any Product -> create new
            $productbutton = new Productbutton;
            $productbutton->senderid = $senderid;
            $productbutton->productid = $productid;
            $productbutton->save();
        }
        return $res->withJson($productbutton);
    });
});


function createSkript($id){
    return "example".$id."text";
}