<?php
        include_once "../../files/config.php";
        include_once "../product/product.php";
        include_once "../stock/avco.php";
        include_once "../purchase_order/purchaseorder.php";
        include_once "../purchase_order/purchaseorderitem.php";
        include_once "../GRN/grn.php";
        class grn_item
        {
            public $grn_item_id;
            public $grn_item_grnid;
            public $grn_item_productid;
            public $grn_item_qty;
            public $grn_item_remain_qty;
            public $grn_item_price;
            public $grn_item_discount;
            public $product_batch;
            public $grn_qty;
        
            public $grn_item_status;
            public $db;


            function __construct()
            {
                $this->db=new mysqli(host,un,pw,db1);
                return true;
            }

            function insert_grnitem($id)
            {
                $avco_product2=new avco();
                $purchaseorderitem5=new purchaseorderitem();
                $purchaseorder5=new purchaseorder();
                $grn_item3=new grn_item();
                $grn_list=0;
            
               foreach($_POST['grn_itemid'] as $item)
                {
                    
                    // $grn_batch=$grn_item3->product_batch($_POST['grn_itemid'][$grn_list]);
                    // exit();
                    $SQL="INSERT INTO grn_item (grn_item_grnid,grn_item_productid,grn_item_qty,grn_item_remain_qty,grn_item_price,grn_item_discount,product_batch)
                    VALUES ($id,'".$_POST['grn_itemid'][$grn_list]."','".$_POST['grn_item_qty'][$grn_list]."','".$_POST['grn_item_qty'][$grn_list]."','".$_POST['grn_itemprice'][$grn_list]."',
                    '".$_POST['grn_item_discount'][$grn_list]."','".$grn_item3->product_batch($_POST['grn_itemid'][$grn_list])."')";
                    $this->db->query($SQL);
                    if($_POST['grn_itemid_inventory'][$grn_list]=='AVCO'){
                        
                        $avco_product2->updatecostprice($_POST['grn_itemid'][$grn_list]);
                       $resultcost=$avco_product2->get_costpricebyid($_POST['grn_itemid'][$grn_list]);
                        $sql_stock="INSERT INTO stock(stock_transactiontype, stock_transactiotypeid,stock_productid,stock_qty,stock_costprice) VALUES ('GRN',$id,'".$_POST['grn_itemid'][$grn_list]."','".$_POST['grn_item_qty'][$grn_list]."',$resultcost)";
                        $this->db->query( $sql_stock);
                        echo $sql_stock;
                    }elseif($_POST['grn_itemid_inventory'][$grn_list]=='FIFO'){
                            $costprice=round(($_POST['grn_itemprice'][$grn_list] * 1) - ($_POST['grn_itemprice'][$grn_list] *1* $_POST['grn_item_discount'][$grn_list]/100),2);
                            $sql_stock="INSERT INTO stock(stock_transactiontype, stock_transactiotypeid,stock_productid,stock_qty,stock_costprice) VALUES ('GRN',$id,'".$_POST['grn_itemid'][$grn_list]."','".$_POST['grn_item_qty'][$grn_list]."',$costprice)";
                        $this->db->query( $sql_stock);
                    }
                    $purchaseorderitem5->update_po_item_currentstatus($_POST['poitem_id'][$grn_list],$_POST['CurrentStatus'][$grn_list]);
                    $purchaseorder5->purchase_order_status($_POST['po_id'][$grn_list]);

                    
                    $grn_list++;
                }
                return true;
            }

             // Product batch
             function product_batch($prod_id)
             {
                 $sql="SELECT COUNT(*) AS count FROM `grn_item` WHERE grn_item_productid=$prod_id";
                 $result = $this->db->query($sql);
                //  echo $result;
                 $count= 0;
         
         
                 while($row=$result->fetch_array()){
         
                     $count = $row["count"];
                 }
                 $count = $count + 1 ;
                 $count = sprintf("%04d", $count);
                 $code = "PB".$prod_id.$count; 
                 
                  
                 echo $code;
                 return $code;
 
 
             }

            function edit_grnitem()
            {
                $grn_list=0;
                foreach($_POST['grn_item_qty'] as $item)
                {
                    $SQL="UPDATE grn_item SET grn_item_qty='".$_POST['grn_item_qty'][$grn_list]."',grn_item_price='".$_POST['grn_itemprice'][$grn_list]."',
                    grn_item_discount='".$_POST['grn_item_discount'][$grn_list]."' WHERE grn_item_id='".$_POST['grn_itemid'][$grn_list]."' ";
                    echo $SQL;
                    $this->db->query($SQL);
                    $grn_list++;
                }
                return true;
            }

            function get_grnitem_byid($id)
            {
                // $SQL="SELECT * FROM grn_item WHERE grn_item_grnid=$id AND grn_item_status='ACTIVE'";
                $SQL="SELECT grn_item.grn_item_id, grn_item.grn_item_grnid, grn_item.grn_item_productid, grn_item.grn_item_qty, grn_item.grn_item_price, grn_item.grn_item_discount,grn_item.product_batch, product.product_name FROM grn_item 
                INNER JOIN product ON grn_item.grn_item_productid=product.product_id WHERE grn_item_grnid=$id AND grn_item_status='ACTIVE'";
                $result=$this->db->query($SQL);
                echo $SQL;
                $grn_item_array=array();
                $prod=new product();
                while($row=$result->fetch_array())
                {
                $grn=new grn_item();
                $grn->grn_item_id=$row["grn_item_id"];
                $grn->grn_item_grnid=$row["grn_item_grnid"];
                $grn->grn_item_prodid=$row["grn_item_productid"];
                $grn->grn_item_prodname=$row["product_name"];
                $grn->grn_item_qty=$row["grn_item_qty"];
                $grn->grn_item_price=$row["grn_item_price"];
                $grn->grn_item_dis=$row["grn_item_discount"];
            
                //afaz
                //$sales_orderitem_item->so_subtotal=round(($row["so_itemqty"]*$row["so_itemprice"]),2);
                $grn->grn_item_subtotoal=round(($row["grn_item_qty"] * $row["grn_item_price"]),2);
                $grn->grn_item_finalprice=round(($row["grn_item_price"] * $row["grn_item_qty"]) - ($row["grn_item_price"] *$row["grn_item_qty"]* $row["grn_item_discount"]/100),2);
                // $grn->grn_item_finalprice=$row["grn_item_price"];
                // $grn->grn_item_status=$row["grn_item_status"];

                $grn_item_array[]=$grn;
                }
                return $grn_item_array;
            }

            // join grn and grnitem
            function grnitem_by_loc($loc)
            {
                $SQL="SELECT grn.grn_id,grn.grn_puch_orderid,grn.grn_ref_no,grn.grn_received_loc,grn.grn_status,grn.grn_date,grn_item.grn_item_id, grn_item.grn_item_grnid, grn_item.grn_item_productid, grn_item.grn_item_qty, grn_item.grn_item_price, grn_item.grn_item_discount,grn_item.product_batch,product.product_name
                FROM grn INNER JOIN grn_item ON grn.grn_id=grn_item.grn_item_grnid INNER JOIN product ON grn_item.grn_item_productid=product.product_id WHERE grn_status='ACTIVE' AND grn_received_loc='$loc'";
                $result=$this->db->query($SQL);
                // echo ($SQL);
                $grn_item_array=array();

                while($row=$result->fetch_array())
                {
                    $grn=new grn_item();
                    $grn->grn_item_id=$row["grn_item_id"];
                    $grn->grn_item_grnid=$row["grn_item_grnid"];
                    $grn->grn_item_productid=$row["grn_item_productid"];
                    $grn->product_batch=$row["product_batch"];
                    $grn->grn_item_prodname=$row["product_name"];
                    $grn->grn_item_qty=$row["grn_item_qty"];
                    $grn->grn_item_price=$row["grn_item_price"];
                    $grn->grn_item_dis=$row["grn_item_discount"];
                    $grn->grn_item_subtotoal=round(($row["grn_item_qty"] * $row["grn_item_price"]),2);
                    $grn->grn_item_finalprice=round(($row["grn_item_price"] * $row["grn_item_qty"]) - ($row["grn_item_price"] *$row["grn_item_qty"]* $row["grn_item_discount"]/100),2);
                
                    $grn_item_array[]=$grn;
                }
                return $grn_item_array;
            }
                
            //    get the grn batch according to the product
            function get_prodbatch_byprod($prodid)
            {
                $SQL="SELECT * FROM grn_item WHERE grn_item_productid='$prodid' AND grn_item_status='ACTIVE'";
                $result=$this->db->query($SQL);
                $grn_item_array=array();

                while($row=$result->fetch_array())
                {
                    $grn=new grn_item();
                    $grn->grn_item_id=$row["grn_item_id"];
                    $grn->grn_item_productid=$row["grn_item_productid"];
                    $grn->product_batch=$row["product_batch"];
                    $grn_item_array[]=$grn;
                }
                return $grn_item_array;
            }

             //    get the grn qty according to the product batch
             function get_grnqty_byprodbatch($prodbatch)
             {
                 $SQL="SELECT * FROM grn_item WHERE product_batch='$prodbatch' AND grn_item_status='ACTIVE'";
                 $result=$this->db->query($SQL);
                 $grn_item_array=array();
 
                 while($row=$result->fetch_array())
                 {
                     $grn=new grn_item();
                     $grn->grn_item_id=$row["grn_item_id"];
                     $grn->grn_item_productid=$row["grn_item_productid"];
                     $grn->grn_item_remain_qty=$row["grn_item_remain_qty"];
                     $grn->grn_item_qty=$row["grn_item_qty"];
 
                     $grn_item_array[]=$grn;
                 }
                 return $grn_item_array;
             }
                
            function item_remaining_stock_productid($productid,$locationid)
            {
                //$sql="SELECT SUM(grn_item_remain_qty) AS totqty FROM grn_item WHERE grn_item_productid= $productid ";
                $sql="SELECT SUM(grn_item_remain_qty) AS totqty FROM grn JOIN grn_item ON grn.grn_id=grn_item.grn_item_grnid WHERE grn.grn_received_loc=$productid AND grn_item.grn_item_productid=$locationid";
               // echo $sql;
                $result_qty=$this->db->query($sql);
                $row=$result_qty->fetch_array();
                 
                $grn_item1=new grn_item();
                $grn_item1->grn_qty=$row['totqty'];
                return $grn_item1;
            }


        }

?>