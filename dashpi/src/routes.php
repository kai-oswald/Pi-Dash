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
    // Render index view
    return $this->renderer->render($res, 'index.phtml', $args);
});

$app->get("/cart", function($req, $res, $args) {
    return $this->renderer->render($res, "cart.phtml", $args);
});
$app->get("/config", function($req, $res, $args) {
    $sender = \Sender::all();
    return $this->renderer->render($res, "config.phtml", array("sender"=>$sender->toArray()));
});

$app->get("/products", function($req, $res, $args) {
    return $this->renderer->render($res, "products.phtml", $args);
});

$app->get("/status", function($req, $res, $args) {
    return $this->renderer->render($res, "status.phtml", $args);
});

// REST API
$app->group("/api", function() use ($app) {  

    $app->get("/products/", function($req, $res, $args) {
        return $res->withJson(Product::all());
    });
    
    $app->get("/products/{id}", function($req, $res, $args){
        $product = \Product::find($args["id"]);
        if(sizeof($product) == 0) {
            return $res->withJson(CreateErrorMessage($args["id"]), 404);
        }
        return $res->withJson($product);
    });

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


    $app->get("/cart/", function($req, $res, $args) {
        $cart = \Cart::all();
        $currentcart = array();
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
            $column = 'senderid';
            $productsender = \Productbutton::where($column, '=', $args["senderid"])->first();
            if(sizeof($productsender) == 0)
            {
                $msg = "Sender with id " . $args["senderid"] . "in table Productbuttons not found.";
                return $res->withJson($msg, "404");
            }
            else
            {
                $productid = $productsender->productid;
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
    
    $app->get("/config/", function($req, $res, $args) {
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
    
    $config = new stdClass();
    $config->ip = "127.0.0.1:8080/";
    
     $app->get("/api/server/status", function($req, $res, $args) {
        $url = "server/status";
        $status = file_get_contents($config->ip . $url);
        return $res->withJson($status);
    });
    
     $app->get("/api/server/tcp/start", function($req, $res, $args) {
        $url = "server/tcp/start";
        $status = file_get_contents($config->ip . $url);
        return $res->withJson($status);
    });
     $app->get("/api/server/udp/start", function($req, $res, $args) {
        $url = "server/udp/start";
        $status = file_get_contents($config->ip . $url);
        return $res->withJson($status);
    });
});