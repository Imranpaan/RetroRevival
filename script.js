document.addEventListener("DOMContentLoaded", function () {

    //register form validation
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            const name = document.querySelector("input[name='User_Name']").value.trim();
            const email = document.querySelector("input[name='User_Email']").value.trim();
            const password = document.querySelector("input[name='User_Password']").value.trim();

            if (name === "" || email === "" || password === "") {
                alert("Please fill in all required fields.");
                event.preventDefault();
                return;
            }

            if (!email.includes("@") || !email.includes(".")) {
                alert("Please enter a valid email address.");
                event.preventDefault();
                return;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters long.");
                event.preventDefault();
                return;
            }
        });
    }

    //login form validation
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            const email = document.querySelector("input[name='User_Email']").value.trim();
            const password = document.querySelector("input[name='User_Password']").value.trim();

            if (email === "" || password === "") {
                alert("Please enter both email and password.");
                event.preventDefault();
            }
        });
    }

    //product upload/edit form validation
    const productForm = document.getElementById("productForm");
    if (productForm) {
        productForm.addEventListener("submit", function (event) {
            const productName = document.querySelector("input[name='Product_Name']").value.trim();
            const price = parseFloat(document.querySelector("input[name='Product_Price']").value);
            const stock = parseInt(document.querySelector("input[name='Product_Stock']").value);

            if (productName === "") {
                alert("Product name is required.");
                event.preventDefault();
                return;
            }

            if (isNaN(price) || price <= 0) {
                alert("Product price must be more than 0.");
                event.preventDefault();
                return;
            }

            if (isNaN(stock) || stock < 0) {
                alert("Stock cannot be negative.");
                event.preventDefault();
                return;
            }
        });
    }

    //checkout form validation
    const checkoutForm = document.getElementById("checkoutForm");
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function (event) {
            const fullName = document.querySelector("input[name='fullName']").value.trim();
            const address = document.querySelector("input[name='address']").value.trim();

            if (fullName === "" || address === "") {
                alert("Please complete your shipping details.");
                event.preventDefault();
            }
        });
    }
});

//delete confirmation
function confirmDeleteProduct() {
    return confirm("Are you sure you want to mark this product as sold out?");
}