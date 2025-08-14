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
                                    <div class="col-md-8">
                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Daftar Produk</h3>
                                            </div>
                                            <div class="box-body">
                                                <div id="produk_list" class="row"></div>
                                            </div>
                                        </div>
                                    </div>
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
                                    
                                </div>
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

<?php include '../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();

    let cart = {}; // Objek untuk menyimpan item di keranjang

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

    $('#kategori_produk').on('change', function() {
        var kategoriId = $(this).val();
        loadProduk(kategoriId);
    });

    loadProduk($('#kategori_produk').val());

    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return ribuan;
    }

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
                alert('Jumlah tidak boleh kurang dari 1.');
                $(this).val(cart[productId].qty);
                return;
            }

            if (newQty > maxStock) {
                alert('Jumlah melebihi stok yang tersedia (' + maxStock + ')!');
                $(this).val(cart[productId].qty);
                return;
            }

            cart[productId].qty = newQty;
            renderCart();
        });
    }

    // Logika utama tombol "Tambah"
    $(document).on('click', '.tambah-produk', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = parseFloat($(this).data('price'));
        const productStock = parseInt($(this).data('stock'));

        if (productStock <= 0) {
            alert('Stok produk habis!');
            return;
        }

        if (cart[productId]) {
            alert('Produk sudah ada di keranjang!');
            return;
        }

        // Tambahkan item ke objek cart
        cart[productId] = {
            name: productName,
            price: productPrice,
            qty: 1, // Kuantitas default
            stock: productStock
        };

        renderCart();
    });
});
</script>