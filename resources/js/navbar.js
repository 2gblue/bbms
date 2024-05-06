//read role of logged in user
document.addEventListener("DOMContentLoaded", function () {
  var link = document.getElementById("bookCatalogueLink");
  var role = link.getAttribute("data-role");

  if (role === "1") {
    //role is user
    link.href = "./bookCatalogue.php";
  } else if (role === "2") {
    //role is staff
    link.href = "./bookCatalogueManage.php";
  }
});
