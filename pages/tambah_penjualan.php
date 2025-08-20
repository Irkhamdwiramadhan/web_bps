<?php 
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil data pegawai dari database
$query_pegawai = "SELECT id, nama FROM pegawai";
$result_pegawai = mysqli_query($koneksi, $query_pegawai);

// Ambil data kategori dari database
$query_kategori = "SELECT id, name FROM categories";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>

<div class="main-content">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i> Tambah Penjualan
        </h1>
    
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Formulir Penjualan</h3>
                    </div>
                    <div class="box-body">
                       <div class="form-card">
                         <form id="form_penjualan" method="POST" action="../proses/proses_tambah_penjualan.php">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kategori">Pilih Kategori Produk:</label>
                                        <select class="form-control select2" id="kategori_produk" style="width: 100%;">
                                            <option value="">-- Semua Kategori --</option>
                                            <?php while ($row = mysqli_fetch_assoc($result_kategori)) { ?>
                                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Daftar Produk</h3>
                                        </div>
                                        <div class="box-body">
                                            <div id="produk_list" class="produk-grid-penjualan row"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <!-- Input Tanggal Transaksi -->
                                    <div class="form-group">
                                        <label for="tanggal_transaksi">Tanggal Transaksi:</label>
                                        <input type="datetime-local" class="form-control" 
                                               name="tanggal_transaksi" 
                                               id="tanggal_transaksi" 
                                               value="<?php echo date('Y-m-d\TH:i'); ?>" 
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label for="pegawai">Pilih Pegawai: (isi produk terlebih dulu)</label>
                                        <select class="form-control select2" name="pegawai_id" required style="width: 100%;">
                                            <option value="">-- Pilih Pegawai --</option>
                                            <?php while ($row = mysqli_fetch_assoc($result_pegawai)) { ?>
                                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nama']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <hr>
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Produk yang Dibeli</h3>
                                    </div>
                                    <div class="box-body no-padding">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th style="width: 100px;">Jumlah</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Subtotal</th>
                                                    <th style="width: 50px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabel_item_penjualan"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right"><h4><b>Total Harga:</b></h4></td>
                                                    <td colspan="2"><h4><b id="total_harga">Rp 0</b></h4></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="box-footer text-right">
                                <input type="hidden" name="total_penjualan" id="input_total_penjualan">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-save"></i> Simpan Penjualan
                                </button>
                            </div>
                        </form>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Custom Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Notifikasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="messageModalBody">
        <!-- Message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 on dropdowns
    $('.select2').select2();

    let cart = {}; // Object to hold items in the cart

    // Function to show a custom modal message
    function showMessageModal(message) {
        $('#messageModalBody').text(message);
        $('#messageModal').modal('show');
    }

    function loadProduk(kategoriId) {
        $.ajax({
            url: '../proses/ambil_produk_by_kategori.php', 
            type: 'GET',
            data: { kategori_id: kategoriId },
            success: function(data) {
                $('#produk_list').html(data);
            }
        });
    }

    // Load products when category changes
    $('#kategori_produk').on('change', function() {
        var kategoriId = $(this).val();
        loadProduk(kategoriId);
    });

    // Initial load of all products
    loadProduk($('#kategori_produk').val());

    // Function to format numbers to Rupiah format
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return ribuan;
    }

    // Function to render the cart table
    function renderCart() {
        $('#tabel_item_penjualan').empty();
        let total = 0;

        for (const productId in cart) {
            const item = cart[productId];
            const subtotal = item.price * item.qty;
            total += subtotal;

            const newRow = `
                <tr data-id="${productId}">
                    <td>${item.name}</td>
                    <td>
                        <input type="number" class="form-control qty_input" name="produk[${productId}][qty]" value="${item.qty}" min="1" max="${item.stock}">
                    </td>
                    <td class="price_item" data-price="${item.price}">Rp ${formatRupiah(item.price)}</td>
                    <td class="subtotal">Rp ${formatRupiah(subtotal)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm hapus-item" data-id="${productId}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#tabel_item_penjualan').append(newRow);
        }

        $('#total_harga').text('Rp ' + formatRupiah(total));
        $('#input_total_penjualan').val(total); 

        // Update event listeners for new rows
        attachCartEventListeners();
    }

    // Function to attach event listeners to cart items
    function attachCartEventListeners() {
        $('.hapus-item').off('click').on('click', function() {
            const productId = $(this).data('id');
            delete cart[productId];
            renderCart();
        });

        $('.qty_input').off('change').on('change', function() {
            const productId = $(this).closest('tr').data('id');
            const newQty = parseInt($(this).val());
            const maxStock = parseInt($(this).attr('max'));
            
            if (isNaN(newQty) || newQty < 1) {
                showMessageModal('Jumlah tidak boleh kurang dari 1.');
                $(this).val(cart[productId].qty);
                return;
            }

            if (newQty > maxStock) {
                showMessageModal('Jumlah melebihi stok yang tersedia (' + maxStock + ')!');
                $(this).val(cart[productId].qty);
                return;
            }

            cart[productId].qty = newQty;
            renderCart();
        });
    }

    // Main logic for the "Tambah" button
    $(document).on('click', '.tambah-produk', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = parseFloat($(this).data('price'));
        const productStock = parseInt($(this).data('stock'));

        if (productStock <= 0) {
            showMessageModal('Stok produk habis!');
            return;
        }

        if (cart[productId]) {
            showMessageModal('Produk sudah ada di keranjang!');
            return;
        }

        // Add item to the cart object
        cart[productId] = {
            name: productName,
            price: productPrice,
            qty: 1, // Default quantity
            stock: productStock
        };

        renderCart();
    });
});
</script>
<style>
    /* Styling untuk daftar produk dalam form */
    .produk-grid-penjualan {
        display: grid;
        /* Menggunakan `repeat(auto-fit, ...)` agar lebih fleksibel saat ada sedikit item */
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    
    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        /* Tambahkan ini untuk memastikan tinggi card seragam */
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-4px);
    }

    /* Styling untuk gambar produk */
    .product-image {
        width: 100%;
        /* Mengatur aspek rasio gambar agar seragam */
        aspect-ratio: 1 / 1; 
        /* Pastikan gambar terpotong dan mengisi ruang */
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    
    .product-card h4 {
        margin: 5px 0;
        font-weight: 600;
        color: #333;
    }
    
    .product-card p {
        margin: 0;
        font-size: 0.9rem;
        color: #666;
    }
    
    .product-card .btn-tambah {
        margin-top: 10px;
        width: 100%;
    }

    /* Styling untuk footer card */
    .card-footer {
        margin-top: auto; /* Mendorong footer ke bawah */
    }
    
    .form-card {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .form-card .form-group {
        margin-bottom: 20px;
    }

    .box-header.with-border h3.box-title {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .table {
        margin-top: 20px;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead th {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }
    
    .table tbody td {
        vertical-align: middle;
    }
    
    .table tfoot td {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .qty_input {
        width: 70px;
        text-align: center;
    }

    .text-right {
        text-align: right;
    }
    
    .btn-lg {
        font-size: 1.1rem;
        padding: 10px 20px;
    }

    /* Modal Styling */
    .modal {
        display: none; /* Hide by default */
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
        background-color: rgba(0,0,0,0.5); /* Semi-transparent overlay */
    }

    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem auto;
    }
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 3.5rem);
    }

    .modal-content {
        position: relative;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 12px;
        outline: 0;
        width: 90%; /* Adjust width for mobile */
        max-width: 500px;
        margin: 0 auto;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
    }
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding: 1rem;
        border-top: 1px solid #e9ecef;
    }
    .close {
        font-size: 1.5rem;
    }
</style>
