$("input[name='qty']").TouchSpin();
$(document).on("click", ".add_to_cart,.add_to_cart_product", function () {
    $btn = $(this);
    var productid = $btn.attr("data-product");
    var productprice = $btn.attr("data-product-price");
    var productname = $btn.attr("data-product-name");
    var productimage = $btn.attr("data-product-image");
    var productqty = $btn.attr("data-product-qty");
    var productcategory = $btn.attr("data-category");
    var vatprice = $btn.attr("data-vat-price");
    $.ajax({
        url: SITE_URL + "cart/add",
        method: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            productid: productid,
            productprice: productprice,
            productname: productname,
            productimage: productimage,
            productqty: productqty,
            productcategory: productcategory,
            vatprice: vatprice,
        },
        success: function (response) {
            if (response.status) {
                // toastr.success(response.message)
                console.log(response);
                messageAlert(
                    "Success",
                    response.message,
                    "fa-check",
                    "success",
                    "",
                    "true"
                );
                $("#cart_total_item").html(response.data.item_count);
                $("#cart_total_item_footer").html(response.data.item_count);
                $("#final_amount_footer").html(response.data.final_amount);
            }
        },
    });
});
$(document).on("click", ".remove-from-cart-btn", function () {
    $btn = $(this);
    var rowid = $btn.attr("data-row-id");
    let postCode = $("#postcode").html();
    let Discount_inper = $("#Discount_inper").html();
    let Discount_type = $("#Discount_type").html();
    // alert(rowid);
    $.ajax({
        url: SITE_URL + "cart/remove-item",
        method: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            rowid: rowid,
            postcode: postCode,
            Discount_inper: Discount_inper,
            Discount_type: Discount_type,
        },
        success: function (response) {
            if (response.status) {
                let cartItem = $(`.cartItem[for='${$btn.attr("data-row-id")}'`);
                cartItem.remove();
                // $("#cart_total_item").html(response.data.item_count);
                // $("#cartTotal").html(response.data.cart_total);
                // $('#FinalAmount').html(response.data.final_amount);
                // $('#DeliveryCharge').html(response.data.delivery_charge);
                let value = response.data;
                $("#cart_total_item").html(response.data.item_count);
                $("#cartTotal").html(response.data.cart_total_price);
                $("#DeliveryCharge").html(response.data.delivery_charge);
                $("#FinalAmount").html(response.data.finalamount);
                if (response.data.finalamount_withdiscount <= 0) {
                    $("#Discount").html("0.00");
                    $("#withDiscount_FinalAmount").html(
                        response.data.finalamount
                    );
                } else {
                    $("#Discount").html(response.data.discountamount);
                    $("#withDiscount_FinalAmount").html(
                        response.data.finalamount_withdiscount
                    );
                }

                if (response.data.item_count == 0) {
                    let html =
                        '<div class="col-lg-12 d-flex justify-content-center"><div class="text-center"><h3 style="padding:30px;">Your cart is empty! </br></h3><a href="' +
                        SITE_URL +
                        '" class="btn btn-primary">Continue to add Products</a></div></div>';
                    $("#cartItems").html(html);
                    $(".shadow-none").hide();
                }
            }
        },
    });
});
$(document).on("change", ".itemTotal", function () {
    $btn = $(this);
    let productPrice = $btn.attr("data-price");
    let cartId = $btn.attr("data-id");
    let vatPrice = $btn.attr("data-vatprice");
    let postCode = $("#postcode").html();
    let Discount_inper = $("#Discount_inper").html();
    let Discount_type = $("#Discount_type").html();

    $.ajax({
        url: SITE_URL + "cart/update-item-qty",
        method: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            cart_id: cartId,
            cart_qty: $btn.val(),
            product_price: productPrice,
            vat_price: vatPrice,
            postcode: postCode,
            Discount_inper: Discount_inper,
            Discount_type: Discount_type,
        },
        success: function (response) {
            if ((response.status = true)) {
                if ($btn.val() == 0) {
                    let cartItem = $(`.cartItem[for='${$btn.attr("data-id")}'`);
                    cartItem.remove();
                }
                let value = response.data;
                $("#cartItemTotal" + cartId).html(value.item_total_cost);
                $("#cart_total_item").html(response.data.item_count);
                $("#cartTotal").html(response.data.cart_total_price);
                $("#DeliveryCharge").html(response.data.delivery_charge);
                $("#FinalAmount").html(response.data.finalamount);
                if (response.data.finalamount_withdiscount <= 0) {
                    $("#Discount").html("0.00");
                    $("#withDiscount_FinalAmount").html(
                        response.data.finalamount
                    );
                } else {
                    $("#Discount").html(response.data.discountamount);
                    $("#withDiscount_FinalAmount").html(
                        response.data.finalamount_withdiscount
                    );
                }
                $("#vattax" + cartId).html(response.data.item_vat);
                $("#vattaxamount" + cartId).html(response.data.item_vat_cost);
                if (response.data.item_count == 0) {
                    let html =
                        '<div class="col-lg-12 d-flex justify-content-center"><div class="text-center"><h3 style="padding:30px;">Your cart is empty! </br></h3><a href="' +
                        SITE_URL +
                        '" class="btn btn-primary">Continue to add Products</a></div></div>';
                    $("#cartItems").html(html);
                    $(".shadow-none").hide();
                }
            } else {
                alert(response.message);
            }
        },
    });
});
function getAddressList() {
    $.ajax({
        url: SITE_URL + "customer/select_address",
        type: "GET",
        success: function (obj) {
            $("#addressList").modal("show");
            $("#body_content").html("");
            $("#body_content").html(obj);
        },
    });
}
function updateDefaultAddress(address_id, address, postcode) {
    loader_show();
    let Discount_inper = $("#Discount_inper").html();
    let Discount_type = $("#Discount_type").html();
    $this = $(this);
    $.ajax({
        url: SITE_URL + "customer/setdefaultaddress",
        type: "POST",
        data: {
            address_id: address_id,
            _token: $("meta[name=csrf-token]").attr("content"),
            Discount_inper: Discount_inper,
            Discount_type: Discount_type,
            postcode,
            postcode,
        },
        success: function (response) {
            loader_hide();
            if (response.status == false && response.type == "VALIDATION") {
                $(".error").text("");
                $(".form-control").removeClass("is-invalid");
                for (key in response.errors) {
                    $.alert(response.errors[key]);
                }
            } else if (response.status == false && response.type == "SYSTEM") {
                $.alert(response.msg);
            } else {
                $("#addressList").modal("hide");
                $("#defaultAddress").html(address);
                $("#defaultAddressId").html(address_id);
                $("#postcode").html(postcode);
                $("#cart_total_item").html(response.data.item_count);
                $("#cartTotal").html(response.data.cart_total_price);
                $("#DeliveryCharge").html(response.data.delivery_charge);
                $("#FinalAmount").html(response.data.finalamount);
                if (response.data.finalamount_withdiscount <= 0) {
                    $("#Discount").html("0.00");
                    $("#withDiscount_FinalAmount").html(
                        response.data.finalamount
                    );
                } else {
                    $("#Discount").html(response.data.discountamount);
                    $("#withDiscount_FinalAmount").html(
                        response.data.finalamount_withdiscount
                    );
                }
                messageAlert(
                    "Success",
                    "Delivery Address updated",
                    "fa-check",
                    "success"
                );
            }
        },
    });
}
$("#addUpdateAddressForm").submit(function (e) {
    $("#addUpdateAddressForm .is-invalid").removeClass("is-invalid");
    $("#addUpdateAddressForm .text-danger").remove();
    let fromData = $("#addUpdateAddressForm").serialize();
    loader_show();
    $.ajax({
        url: SITE_URL + "customer/addupdateaddress",
        type: "POST",
        data: fromData,
        success: function (obj) {
            loader_hide();
            if (obj.status == false && obj.type == "VALIDATION") {
                $(".error").text("");
                $(".form-control").removeClass("is-invalid");
                for (key in obj.errors) {
                    $("#addUpdateAddressForm #" + key).addClass("is-invalid");
                    $("#addUpdateAddressForm #" + key).after(
                        '<p class="text-danger mb-0">' +
                            obj.errors[key] +
                            "</p>"
                    );
                }
            } else if (obj.status == false && obj.type == "SYSTEM") {
                $.alert(obj.msg);
            } else if (obj.status == false && obj.type == "InvalidAddress") {
                $.confirm({
                    title: "",
                    content: obj.msg,
                    closeIcon: true,
                    buttons: {
                        confirm: {
                            text: "ok",
                            btnClass: "btn-primary",
                            action: function () {
                                $("#addUpdateAddress").modal("hide");
                                $("#addManualAddress").modal("show");
                                $("#addManualAddress #houseno").val(
                                    obj.house_no
                                );
                                $("#addManualAddress #postcode").val(
                                    obj.postcode
                                );
                            },
                        },
                        Reject: {
                            text: "Cancel",
                            btnClass: "btn-secondary",
                            action: function () {},
                        },
                    },
                });
            } else if (obj.status == false && obj.type == "NotValid") {
                $.alert(obj.msg);
            } else {
                getAddressList();
                if (obj.total_count == 1) {
                    $("#defaultAddress").html(obj.address);
                    $("#defaultAddressId").html(obj.address_id);
                    $("#postcode").html(obj.postcode);
                }
                $("#addUpdateAddress").modal("hide");
                messageAlert("Success", obj.msg, "fa-check", "success");
                $("#addUpdateAddressForm")[0].reset();
                // $('#addressList').modal('hide');
                // $('#defaultAddress').html(obj.address);
            }
        },
    });
    return false;
});
$("#addUpdateAddress").on("hide.bs.modal", function (e) {
    $("#addUpdateAddressForm")[0].reset();
});
$("#addManualAddressForm").submit(function (e) {
    $("#addManualAddressForm .is-invalid").removeClass("is-invalid");
    $("#addManualAddressForm .text-danger").remove();
    let fromData = $("#addManualAddressForm").serialize();
    // loader_show();
    $.ajax({
        url: SITE_URL + "customer/addmanualaddress",
        type: "POST",
        data: fromData,
        success: function (obj) {
            // loader_hide();
            // console.log(obj)
            // return
            if (obj.status == false && obj.type == "VALIDATION") {
                $(".error").text("");
                $(".form-control").removeClass("is-invalid");
                for (key in obj.errors) {
                    $("#addManualAddressForm #" + key).addClass("is-invalid");
                    $("#addManualAddressForm #" + key).after(
                        '<p class="text-danger mb-0">' +
                            obj.errors[key] +
                            "</p>"
                    );
                }
            } else {
                getAddressList();
                $("#addManualAddress").modal("hide");
                messageAlert("Success", obj.msg, "fa-check", "success");
            }
        },
    });
    return false;
});

function placeorder() {
    loader_show();
    let delivery_charge = $("#DeliveryCharge").html();
    let final_amount = $("#FinalAmount").html();
    let Discount = $("#Discount").html();
    let withDiscount_FinalAmount = $("#withDiscount_FinalAmount").html();
    let Discount_type = $("#Discount_type").html();
    let Discount_inper = $("#Discount_inper").html();
    let promo_code = $("#promo_code").html();

    $.ajax({
        url: SITE_URL + "order/placeorder",
        type: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            delivery_charge: delivery_charge,
            final_amount: final_amount,
            Discount: Discount,
            withDiscount_FinalAmount: withDiscount_FinalAmount,
            Discount_type: Discount_type,
            Discount_inper: Discount_inper,
            promo_code: promo_code,
        },
        success: function (response) {
            if (response.status == true) {
                loader_hide();
                window.location.href = response.identity_response[0];
                // $(document).Toasts('create', {
                //   class: 'bg-success',
                //   title: 'Success',
                //   subtitle: '',
                //   body: response.message
                // })
                console.log(response);
                //   setTimeout(function () {
                //     window.location.href = SITE_URL+'cart';
                //  }, 2000);
            }
            if (response.status == false) {
                loader_hide();
                if (response.type == "invalidPostcode") {
                    // $(document).Toasts('create', {
                    // class: 'bg-danger',
                    // title: 'Invalid Post Code',
                    // subtitle: '',
                    // body: response.message
                    //  })
                    $.alert(response.message);
                }
                if (response.type == "InvalidAmount") {
                    // $(document).Toasts('create', {
                    //   class: 'bg-danger',
                    //   title: 'Alert',
                    //   subtitle: '',
                    //   body: response.message
                    //    })
                    $.alert(response.message);
                }

                if (response.type == "NoAddress") {
                    $("#addUpdateAddress").modal("show");
                }
            }
        },
    });
}

function guestcheckout() {
    loader_show();
    let contact_no = $("#contact_no").val();
    if (contact_no == "") {
        $("#contact_no").addClass("is-invalid");
        $("#contact_no").focus();
        loader_hide();
        return;
    }

    let customer_name = $(".customer_name").val();
    let customer_email = $(".customer_email").val();
    let house_no = $(".house_no").val();
    let post_code = $(".post_code").val();
    let country_code = $("#country_code").val();

    let delivery_charge = $("#DeliveryCharge").html();
    let final_amount = $("#FinalAmount").html();
    let Discount = $("#Discount").html();
    let withDiscount_FinalAmount = $("#withDiscount_FinalAmount").html();
    let Discount_type = $("#Discount_type").html();
    let Discount_inper = $("#Discount_inper").html();
    let promo_code = $("#promo_code").html();

    $.ajax({
        url: SITE_URL + "order/guestcheckout",
        type: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            contact_no: contact_no,
            customer_name: customer_name,
            customer_email: customer_email,
            house_no: house_no,
            post_code: post_code,
            country_code: country_code,
            delivery_charge: delivery_charge,
            final_amount: final_amount,
            Discount: Discount,
            withDiscount_FinalAmount: withDiscount_FinalAmount,
            Discount_type: Discount_type,
            Discount_inper: Discount_inper,
            promo_code: promo_code,
        },
        success: function (response) {
            loader_hide();
            if (response.status == true) {
                CMplaceorder();
            }
            if (response.status == false) {
                // if(response.type == 'validation')
                // {
                //   $('.text-danger').text('');
                //   $('.form-control').removeClass('is-invalid');
                //   for (key in response.errors) {
                //   $('#' + key).addClass('is-invalid');
                //   $("#" + key + "_error").after(
                //     '<p class="text-danger">' + response.errors[key] + "</p>"
                //   );
                //   }
                // }

                if (response.type == "invalidPostcode") {
                    $.alert(response.message);
                }
                if (response.type == "InvalidAmount") {
                    $.alert(response.message);
                }
                $.alert(response.message);
            }
        },
    });
}

$("#post_code").keyup(function () {
    // if( this.value.length < 6 ) return;
    var delivery_charge = $("#DeliveryCharge").html();
    var final_amount = $("#FinalAmount").html();
    var Discount = $("#Discount").html();
    var withDiscount_FinalAmount = $("#withDiscount_FinalAmount").html();

    $.ajax({
        url: SITE_URL + "order/getdeliverycharge",
        type: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            final_amount: final_amount,
            withDiscount_FinalAmount: withDiscount_FinalAmount,
            post_code: $(this).val(),
            delivery_charge: delivery_charge,
        },
        success: function (response) {
            $("#DeliveryCharge").html(response.data.delivery_charge);

            $("#FinalAmount").text(response.data.final_amount);

            $("#withDiscount_FinalAmount").text(
                response.data.withDiscount_FinalAmount
            );
        },
    });
});

function CMplaceorder() {
    loader_show();

    let contact_no = $("#contact_no").val();
    if (contact_no == "") {
        $("#contact_no").addClass("is-invalid");
        $("#contact_no").focus();
        loader_hide();
    } else {
        let note = $("#note").val();
        let delivery_charge = $("#DeliveryCharge").html();
        let final_amount = $("#FinalAmount").html();
        let Discount = $("#Discount").html();
        let withDiscount_FinalAmount = $("#withDiscount_FinalAmount").html();
        let Discount_type = $("#Discount_type").html();
        let Discount_inper = $("#Discount_inper").html();
        let promo_code = $("#promo_code").html();
        let country_code = $("#country_code").val();
        // console.log(delivery_charge);
        $.ajax({
            url: SITE_URL + "order/placeorderCM",
            type: "POST",
            data: {
                _token: $("meta[name=csrf-token]").attr("content"),
                delivery_charge: delivery_charge,
                final_amount: final_amount,
                Discount: Discount,
                withDiscount_FinalAmount: withDiscount_FinalAmount,
                Discount_type: Discount_type,
                Discount_inper: Discount_inper,
                promo_code: promo_code,
                contact_no: contact_no,
                note: note,
                country_code: country_code,
            },
            success: function (response) {
                if (response.status == true) {
                    loader_hide();
                    window.location.href =
                        SITE_URL + "paymentmethod/" + response.orderId;

                    console.log(response);
                }
                if (response.status == false) {
                    loader_hide();
                    if (response.type == "invalidPostcode") {
                        $.alert(response.message);
                    }
                    if (response.type == "InvalidAmount") {
                        $.alert(response.message);
                    }

                    if (response.type == "NoAddress") {
                        $("#addUpdateAddress").modal("show");
                    }
                }
            },
        });
    }
}

function BitpayOrder() {
    loader_show();
    let delivery_charge = $("#DeliveryCharge").html();
    let final_amount = $("#FinalAmount").html();
    let Discount = $("#Discount").html();
    let withDiscount_FinalAmount = $("#withDiscount_FinalAmount").html();
    let Discount_type = $("#Discount_type").html();
    let Discount_inper = $("#Discount_inper").html();
    let promo_code = $("#promo_code").html();

    $.ajax({
        url: SITE_URL + "order/placeorderBit",
        type: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            delivery_charge: delivery_charge,
            final_amount: final_amount,
            Discount: Discount,
            withDiscount_FinalAmount: withDiscount_FinalAmount,
            Discount_type: Discount_type,
            Discount_inper: Discount_inper,
            promo_code: promo_code,
        },
        success: function (response) {
            if (response.status == true) {
                loader_hide();
                window.location.href = response.redirectUrl;

                console.log(response);
            }
            if (response.status == false) {
                loader_hide();
                if (response.type == "invalidPostcode") {
                    $.alert(response.message);
                }
                if (response.type == "InvalidAmount") {
                    $.alert(response.message);
                }

                if (response.type == "NoAddress") {
                    $("#addUpdateAddress").modal("show");
                }
            }
        },
    });
}

function showProductDetailpoup(id) {
    $.ajax({
        url: SITE_URL + "products/getProductDetail",
        type: "POST",
        data:
            "id=" +
            id +
            "&_token=" +
            $("meta[name=csrf-token]").attr("content"),
        success: function (obj) {
            $("#commonModalHtml").html(obj);
            $("#commonModal").modal("show");
        },
    });
}
$(document).on("change", ".customized_value", function () {
    $btn = $(this);
    let productId = $btn.attr("data-productid");
    let vatPrice = $btn.attr("data-vatprice");
    let qty = $btn.val();
    let total = vatPrice * qty;
    $("#vattaxamount" + productId).html(parseFloat(total).toFixed(2));
});
$(document).on("click", ".remove_Customized_Item", function () {
    $btn = $(this);
    let productId = $btn.attr("data-productid");
    let productRowId = $btn.attr("data-rowId");
    let vatprice = $btn.attr("data-vatprice");
    $.ajax({
        url: SITE_URL + "cart/remove_Customized_Item",
        method: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            productid: productId,
            productRowId: productRowId,
        },
        success: function (response) {
            if ((response.status = true)) {
                $("#remove_Customized_Item" + productId).attr(
                    "data-rowId",
                    "0"
                );
                $("#customizeditemTotal" + productId).attr("data-rowId", "0");
                $("#product_qty" + productId).val("1");
                $("#vattaxamount" + productId).html(vatprice);
                $("#remove_Customized_Item" + productId).hide();
                $("#final_amount_footer").html(response.data.final_amount);
                $("#cart_total_item").html(response.data.item_count);
            }
        },
    });
});
$(document).on("click", ".customizeditemTotal", function () {
    $btn = $(this);
    let productPrice = $btn.attr("data-price");
    let productId = $btn.attr("data-productid");
    let vatPrice = $btn.attr("data-vatprice");
    let productname = $btn.attr("data-productname");
    let productimage = $btn.attr("data-product-image");
    let productcategory = $btn.attr("data-category");
    let productRowId = $btn.attr("data-rowId");
    let product_qty = $("#product_qty" + productId).val();
    if (product_qty == 0) {
        $.alert("Atleast add one quntity");
    } else {
        $.ajax({
            url: SITE_URL + "cart/customized-item-qty",
            method: "POST",
            data: {
                _token: $("meta[name=csrf-token]").attr("content"),
                productid: productId,
                productqty: product_qty,
                productprice: productPrice,
                vatprice: vatPrice,
                productname: productname,
                productimage: productimage,
                productcategory: productcategory,
                productRowId: productRowId,
            },
            success: function (response) {
                if ((response.status = true)) {
                    messageAlert(
                        "Success",
                        response.message,
                        "fa-check",
                        "success",
                        "",
                        "true"
                    );
                    $("#vattaxamount" + productId).html(
                        response.data.show_amount
                    );
                    $("#cart_total_item").html(response.data.item_count);
                    $("#cart_total_item_footer").html(response.data.item_count);
                    $("#final_amount_footer").html(response.data.final_amount);
                    $btn.attr("data-rowId", response.data.rowId);
                    $("#remove_Customized_Item" + productId).attr(
                        "data-rowId",
                        response.data.rowId
                    );
                    $("#remove_Customized_Item" + productId).show();
                } else {
                    alert(response.message);
                }
            },
        });
    }
});
function customizedProduct(id) {
    $.ajax({
        url: SITE_URL + "products/customizedProduct",
        type: "POST",
        data:
            "id=" +
            id +
            "&_token=" +
            $("meta[name=csrf-token]").attr("content"),
        success: function (obj) {
            $("#commonModalHtml").html(obj);
            $("#commonModal").modal("show");
        },
    });
}
$(document).on("click", ".add_product_as_favourite", function () {
    $btn = $(this);

    var productid = $btn.attr("data-product");
    var productprice = $btn.attr("data-product-price");
    var productname = $btn.attr("data-product-name");
    var productimage = $btn.attr("data-product-image");
    var productqty = $btn.attr("data-product-qty");
    var productcategory = $btn.attr("data-category");
    var vatprice = $btn.attr("data-vat-price");
    var type = $btn.attr("data-type");
    if (type == "favourite") {
        $("#cardId" + productid).remove();
    }
    $("#cardId" + productid).remove();
    if ($(this).children().attr("class") == "fas fa-heart") {
        $(this).children().removeClass("fas fa-heart");
        $(this).children().addClass("far fa-heart");
    } else {
        $(this).children().removeClass("far fa-heart");
        $(this).children().addClass("fas fa-heart");
    }
    $.ajax({
        url: SITE_URL + "favourite/add",
        method: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
            productid: productid,
            productprice: productprice,
            productname: productname,
            productimage: productimage,
            productqty: productqty,
            productcategory: productcategory,
            vatprice: vatprice,
        },
        success: function (response) {
            if (response.status) {
                // toastr.success(response.message)
                console.log(response);
                messageAlert(
                    "Success",
                    response.message,
                    "fa-check",
                    "success",
                    "",
                    "true"
                );
                $("#favourite_item_total").html(response.data.item_count);
            }
        },
    });
});

$(document).on("click", ".guest_checkout", function () {
    $(".guest_checkout_section").toggleClass("d-none");
    $(".user_checkout_button").toggleClass("d-none");
    $(".guest_checkout_button").toggleClass("d-none");
});
