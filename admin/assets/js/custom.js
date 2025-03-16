$(document).ready(function () {
  alertify.set("notifier", "position", "top-right");

  // Increment Quantity
  $(document).on("click", ".increment", function () {
    var $quantityInput = $(this).closest(".qtyBox").find(".qty");
    var productId = $(this).closest(".qtyBox").find(".prodId").val();
    var currentValue = parseInt($quantityInput.val());

    if (!isNaN(currentValue)) {
      var qtyVal = currentValue + 1;
      updateQuantity(productId, qtyVal, $quantityInput);
    }
  });

  // Decrement Quantity
  $(document).on("click", ".decrement", function () {
    var $quantityInput = $(this).closest(".qtyBox").find(".qty");
    var productId = $(this).closest(".qtyBox").find(".prodId").val();
    var currentValue = parseInt($quantityInput.val());

    if (!isNaN(currentValue) && currentValue > 1) {
      var qtyVal = currentValue - 1;
      updateQuantity(productId, qtyVal, $quantityInput);
    }
  });

  // Update Quantity Function
  function updateQuantity(prodId, qty, $quantityInput) {
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

        if (res.status == 200) {
          // Update only the affected row and grand total
          $quantityInput.val(qty); // Update quantity
          $quantityInput
            .closest("tr")
            .find("td:nth-child(5)") // Update the Total Price column
            .text(res.newTotalPrice);

          // Update Grand Total
          $("#grandTotal").text(res.grandTotal);

          alertify.success(res.message);
        } else {
          alertify.error(res.message);
        }
      },
      error: function () {
        alertify.error("Failed to update quantity. Please try again.");
      },
    });
  }

  // Proceed to place order button click
  $(document).on("click", ".proceedToPlace", function () {
    var cphone = $("#cphone").val();
    var payment_mode = $("#payment_mode").val();

    if (payment_mode == "") {
      Swal.fire({
        title: "Select Payment Mode",
        text: "Select your payment mode",
        icon: "warning",
        confirmButtonText: "OK",
      });
      return false;
    }

    if (cphone == "" || !$.isNumeric(cphone)) {
      Swal.fire({
        title: "Enter Phone Number",
        text: "Enter a valid phone number",
        icon: "warning",
        confirmButtonText: "OK",
      });
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
          Swal.fire({
            title: res.message,
            text: res.message,
            icon: res.status_type,
            showCancelButton: true,
            confirmButtonText: "Add Customer",
            cancelButtonText: "Cancel",
          }).then((result) => {
            if (result.isConfirmed) {
              $("#c_phone").val(cphone);
              $("#addCustomerModal").modal("show");
            } else {
              // Handle cancel
              $("#addCustomerModal").modal("hide");
            }
          });
        } else {
          Swal.fire({
            title: res.message,
            text: res.message,
            icon: res.status_type,
            confirmButtonText: "OK",
          });
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
    console.log("Save Order button clicked"); // Debugging
    $.ajax({
      type: "POST",
      url: "orders-code.php",
      data: { saveOrder: true },
      success: function (response) {
        console.log("Response received: ", response); // Debugging
        try {
          var res = JSON.parse(response);
          console.log("Parsed response: ", res); // Debugging

          if (res.status == 200) {
            swal("Success", "Order placed successfully!", "success");
            $("#orderPlaceSuccessMessage").text(res.message);
            $("#orderSuccessModal").modal("show");
          } else {
            swal("Error", res.message, res.status_type || "error");
          }
        } catch (e) {
          console.error("Error parsing response: ", e); // Debugging
          swal("Error", "Invalid response from server!", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error: ", error); // Debugging
        swal("Error", "Failed to connect to server!", "error");
      },
    });
  });
});

function printMyBillingArea() {
  var divContents = document.getElementById("myBillingArea").innerHTML;
  var a = window.open("", "");
  a.document.write("<html><title>DCS POS System</title>");
  a.document.write("<body style='font-family: Helvetica, Arial, sans-serif;'>");
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
