$(document).ready(function () {
  alertify.set("notifier", "position", "top-right");

  $(document).on("click", ".increment", function () {
    var $quantityInput = $(this).closest(".qtyBox").find(".qty");
    var productId = $(this).closest(".qtyBox").find(".prodId").val();
    var currentValue = parseInt($quantityInput.val());

    if (!isNaN(currentValue)) {
      var qtyVal = currentValue + 1;
      $quantityInput.val(qtyVal);
      quantityIncDec(productId, qtyVal);
    }
  });

  $(document).on("click", ".decrement", function () {
    var $quantityInput = $(this).closest(".qtyBox").find(".qty");
    var productId = $(this).closest(".qtyBox").find(".prodId").val();
    var currentValue = parseInt($quantityInput.val());

    if (!isNaN(currentValue) && currentValue > 1) {
      var qtyVal = currentValue - 1;
      $quantityInput.val(qtyVal);
      quantityIncDec(productId, qtyVal);
    }
  });

  function quantityIncDec(prodId, qty) {
    $.ajax({
      type: "POST",
      url: "orders-code.php",
      data: {
        productIncDec: true,
        product_id: prodId,
        quantity: qty,
      },
      success: function (response) {
        var res = JSON.parse(response);
        // console.log(res);

        if (res.status == 200) {
          // window.location.reload();
          $("#productArea").load(" #productContent");
          alertify.success(res.message);
        } else {
          $("#productArea").load(" #productContent");
          alertify.error(res.message);
        }
      },
    });
  }

  // Proceed to place order button click
  $(document).on("click", ".proceedToPlace", function () {
    var cphone = $("#cphone").val();
    var payment_mode = $("#payment_mode").val();

    if (payment_mode == "") {
      swal("Select Payment Mode", "Select your payment mode", "warning");
      return false;
    }

    if (cphone == "" || !$.isNumeric(cphone)) {
      swal("Enter Phone Number", "Enter a valid phone number", "warning");
      return false;
    }

    var data = {
      proceedToPlaceBtn: true,
      cphone: cphone,
      payment_mode: payment_mode,
    };

    $.ajax({
      type: "POST",
      url: "orders-code.php",
      data: data,
      success: function (response) {
        var res = JSON.parse(response);
        if (res.status == 200) {
          window.location.href = "order-summery.php";
        } else if (res.status == 404) {
          swal(res.message, res.message, res.status_type, {
            buttons: {
              catch: {
                text: "ADD Customer",
                value: "catch",
              },
              cancel: "Cancel",
            },
          }).then((value) => {
            switch (value) {
              case "catch":
                $("#c_phone").val(cphone);
                $("#addCustomerModal").modal("show");
                break;
              default:
                // Handle cancel
                $("#addCustomerModal").modal("hide");
                break;
            }
          });
        } else {
          swal(res.message, res.message, res.status_type);
        }
      },
    });
  });

  // Save customer button click
  $(document).on("click", ".saveCustomer", function () {
    var c_name = $("#c_name").val();
    var c_phone = $("#c_phone").val();
    var c_email = $("#c_email").val();

    if (c_name != "" && c_phone != "") {
      if ($.isNumeric(c_phone)) {
        var data = {
          saveCustomerBtn: true,
          name: c_name,
          phone: c_phone,
          email: c_email,
        };

        $.ajax({
          type: "POST",
          url: "orders-code.php",
          data: data,
          success: function (response) {
            var res = JSON.parse(response);
            if (res.status == 200) {
              swal("Success", "Customer added successfully!", "success").then(
                () => {
                  $("#addCustomerModal").modal("hide"); // Close modal after saving
                }
              );
            } else if (res.status == 422) {
              swal(res.message, res.message, "error");
            } else {
              swal(res.message, res.message, "error");
            }
          },
        });
      } else {
        swal("Enter valid number", "", "warning");
      }
    } else {
      swal("Please fill required fields", "", "warning");
    }
  });

  $(document).on("click", "#saveOrder", function () {
    $.ajax({
      type: "POST",
      url: "orders-code.php",
      data: { saveOrder: true },
      success: function (response) {
        try {
          var res = JSON.parse(response);

          if (res.status == 200) {
            swal("Success", "Order placed successfully!", "success");
            $("#orderPlaceSuccessMessage").text(res.message);
            $("#orderSuccessModal").modal("show");
          } else {
            swal("Error", res.message, res.status_type);
          }
        } catch (e) {
          swal("Error", "Invalid response from server!", "error");
        }
      },
      error: function () {
        swal("Error", "Failed to connect to server!", "error");
      },
    });
  });
});

function printMyBillingArea() {
  var divContents = document.getElementById("myBillingArea").innerHTML;
  var a = window.open("", "");
  a.document.write("<html><title>DCS POS System</title>");
  a.document.write('<body style="font-family: fangsong;">');
  a.document.write(divContents);
  a.document.write("</body></html>");
  a.document.close();
  a.print();
}

window.jsPDF = window.jspdf.jsPDF;
var docPDF = new jsPDF();

function downloadPDF(invoiceNo) {
  var elementHTML = document.querySelector("#myBillingArea");
  docPDF.html(elementHTML, {
    callback: function () {
      docPDF.save(invoiceNo + ".pdf");
    },
    x: 15,
    y: 15,
    width: 170,
    windowWidth: 650,
  });
}

