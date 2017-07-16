<!--Ecommerce-объект-->
<script type="text-javascript">
window.dataLayer = window.dataLayer || [];
</script>

<?php
if(userdata('action_add_to_cart') !== false)
{
    ?>
    <script>
        dataLayer.push({
            "ecommerce": {
                "add": {
                    "products": [
                        {
                            "id": "<?=userdata('action_add_to_cart')?>",
                            "name": "<?=userdata('action_add_to_cart_name')?>",
                            "price": <?=userdata('action_add_to_cart_price')?>,
                            "brand": "Peony",
                            "category": "<?=userdata('action_add_to_cart_category')?>",
                            "quantity": <?=userdata('action_add_to_cart_kolvo')?>
                        }
                    ]
                }
            }
        });
    </script>
    <?php
    unset_userdata('action_add_to_cart');
    unset_userdata('action_add_to_cart_name');
    unset_userdata('action_add_to_cart_price');
    unset_userdata('action_add_to_cart_category');
    unset_userdata('action_add_to_cart_razmer');
    unset_userdata('action_add_to_cart_kolvo');
}
elseif (userdata('action_del') !== false){
    ?>
    <script>
        dataLayer.push({
            "ecommerce": {
                "remove": {
                    "products": [
                        {
                            "id": "<?=userdata('action_del')?>",
                            "name": "<?=userdata('action_del_name')?>",
                            "category": "<?=userdata('action_del_category')?>"
                        }
                    ]
                }
            }
        });
    </script>
<?php
    unset_userdata('action_del');
    unset_userdata('action_del_name');
    unset_userdata('action_del_category');
}
elseif (userdata('action_order') !== false){
    $order = unserialize(userdata('action_order'));
    $products = unserialize($order['products']);
    ?>
    <script>
        dataLayer.push({
            "ecommerce": {
                "purchase": {
                    "actionField": {
                        "id" : "<?=$order['id']?>",
                        "goal_id" : "<?=$order['id']?>"
                    },
                    "products": [
                        <?php
                        $this->load->model('Model_shop','shop');
                        $count = 0;
                        foreach ($products as $product){
                            $p = $this->shop->getArticleById($product['shop_id']);
                            if($p) {
                                $cat = $this->model_categories->getCategoryById($p['category_id']);
                                if($count > 0) echo ',';
                                ?>
                                {
                                    "id": "<?=$product['shop_id']?>",
                                    "name": "<?=$p['name']?>",
                                    "price": <?=$p['price']?>,
                                    "brand": "Peony",
                                    "category": "<?=$cat['name']?>",
                                    "variant": "<?=$p['color']?>"
                                }<?php

                                $count++;
                            }
                        }
                        ?>
                    ]
                }
            }
        });
    </script>
    <?php
    unset_userdata('action_order');
}

if(isset($article) && isset($category) && $category['type'] == 'shop')
{
    ?>
    <script>
        dataLayer.push({
            "ecommerce": {
                "detail": {
                    "products": [
                        {
                            "id": "<?=$article['id']?>",
                            "name" : "<?=$article['name']?>",
                            "price": <?=$article['price']?>,
                            "brand": "Peony",
                            "category": "<?=$category['name']?>",
                            "variant" : "<?=$article['color']?>"
                        }
                    ]
                }
            }
        });
    </script>
<?php
}
