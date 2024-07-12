@extends('layouts.app')

@section('content')
    <h2>Products</h2>

    <form id="productForm">
        @csrf
        <input type="hidden" id="productId">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="price">Price:</label>
        <input type="text" id="price" name="price" required>
        <br>
        <label for="product_code">Product Code:</label>
        <input type="text" id="product_code" name="product_code" required>
        <br>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <br>
        <button type="submit">Save Product</button>
    </form>


    <h3>Product List</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Price</th>
                <th>Product Code</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productList">
     
        </tbody>
    </table>

    <div id="error-message" style="color: red;"></div>

    @if (session('status'))
        <div>{{ session('status') }}</div>
    @endif

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
        
            fetchProducts();

    
            $('#productForm').submit(function(event) {
                event.preventDefault(); 

                let productId = $('#productId').val();
                let formData = {
                    title: $('#title').val(),
                    price: $('#price').val(),
                    product_code: $('#product_code').val(),
                    description: $('#description').val()
                };

                let url = '/api/products';
                let method = 'POST';

                if (productId) {
                    url += '/' + productId;
                    method = 'PUT';
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Product saved successfully!');
                        $('#productForm')[0].reset();
                        $('#productId').val('');
                        fetchProducts();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        let response = JSON.parse(xhr.responseText);
                        $('#error-message').text(response.message);
                    }
                });
            });

            // Fetch and display products
            function fetchProducts() {
                $.ajax({
                    url: '/api/products',
                    method: 'GET',
                    success: function(response) {
                        let products = response.data.products;
                        let productList = $('#productList');
                        productList.empty();
                        products.forEach(product => {
                            productList.append(`
                                <tr>
                                    <td>${product.title}</td>
                                    <td>${product.price}</td>
                                    <td>${product.product_code}</td>
                                    <td>${product.description}</td>
                                    <td>
                                        <button onclick="editProduct(${product.id})">Edit</button>
                                        <button onclick="deleteProduct(${product.id})">Delete</button>
                                    </td>
                                </tr>
                            `);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        let response = JSON.parse(xhr.responseText);
                        $('#error-message').text(response.message);
                    }
                });
            }

            // Edit product
            window.editProduct = function(id) {
                $.ajax({
                    url: '/api/products/' + id,
                    method: 'GET',
                    success: function(response) {
                        let product = response.data.product;
                        $('#productId').val(product.id);
                        $('#title').val(product.title);
                        $('#price').val(product.price);
                        $('#product_code').val(product.product_code);
                        $('#description').val(product.description);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        let response = JSON.parse(xhr.responseText);
                        $('#error-message').text(response.message);
                    }
                });
            };

            // Delete product
            window.deleteProduct = function(id) {
                $.ajax({
                    url: '/api/products/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Product deleted successfully!');
                        fetchProducts();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        let response = JSON.parse(xhr.responseText);
                        $('#error-message').text(response.message);
                    }
                });
            };
        });
    </script>
@endsection
