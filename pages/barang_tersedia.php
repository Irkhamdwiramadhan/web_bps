<?php
// Masukkan file koneksi database dan layout utama
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Menyiapkan variabel untuk filter
$filter_category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$show_promo = isset($_GET['promo']) ? true : false;

// Mengambil data produk
$sql_products = "SELECT p.id, p.name, c.name AS category_name, p.price, p.stock, p.image, p.is_promo, p.promo_price 
                 FROM products p 
                 JOIN categories c ON p.category_id = c.id";

// Menambahkan kondisi WHERE sesuai filter yang dipilih
if ($show_promo) {
    $sql_products .= " WHERE p.is_promo = 1";
} elseif ($filter_category_id) {
    $sql_products .= " WHERE p.category_id = ?";
}

$sql_products .= " ORDER BY p.name ASC";

// Menggunakan Prepared Statement untuk mencegah SQL Injection
$stmt_products = $koneksi->prepare($sql_products);
if ($filter_category_id) {
    $stmt_products->bind_param("i", $filter_category_id);
}
$stmt_products->execute();
$result_products = $stmt_products->get_result();

// Mengambil daftar kategori dan jumlah produk di setiap kategori
$sql_categories_count = "SELECT c.id, c.name, COUNT(p.id) AS product_count 
                         FROM categories c
                         LEFT JOIN products p ON c.id = p.category_id
                         GROUP BY c.id, c.name
                         ORDER BY c.name ASC";
$result_categories_count = $koneksi->query($sql_categories_count);

// Mengambil total produk untuk link 'Semua'
$sql_total_products = "SELECT COUNT(*) AS total_products FROM products";
$result_total_products = $koneksi->query($sql_total_products);
$total_products = $result_total_products->fetch_assoc()['total_products'];

// Mengambil total produk promo
$sql_total_promo = "SELECT COUNT(*) AS total_promo FROM products WHERE is_promo = 1";
$result_total_promo = $koneksi->query($sql_total_promo);
$total_promo = $result_total_promo->fetch_assoc()['total_promo'];
?>

<main class="main-content">
    <div class="header-content">
        <h2>Stok Barang</h2>
        <a href="tambah_barang.php" class="btn btn-primary">Tambah Barang Baru</a>
    </div>

    <div class="content-wrapper">
        <aside class="category-sidebar">
            <h4>Kategori Produk</h4>
            <ul class="category-list">
                <li class="category-item">
                    <a href="barang_tersedia.php" class="category-link <?php echo (!$filter_category_id && !$show_promo) ? 'active' : ''; ?>">
                        <span>Semua</span>
                        <span class='category-count'><?php echo $total_products; ?></span>
                    </a>
                </li>
                <li class="category-item">
                    <a href="barang_tersedia.php?promo=true" class="category-link <?php echo ($show_promo) ? 'active' : ''; ?>">
                        <span>Promo</span>
                        <span class='category-count'><?php echo $total_promo; ?></span>
                    </a>
                </li>
                <?php
                if ($result_categories_count && $result_categories_count->num_rows > 0) {
                    while($row = $result_categories_count->fetch_assoc()) {
                        $active_class = ($row['id'] == $filter_category_id) ? 'active' : '';
                        echo "<li class='category-item'>";
                        echo "<a href='barang_tersedia.php?category_id=" . $row['id'] . "' class='category-link " . $active_class . "'>";
                        echo "<span>" . htmlspecialchars($row['name']) . "</span>";
                        echo "<span class='category-count'>" . htmlspecialchars($row['product_count']) . "</span>";
                        echo "</a>";
                        echo "</li>";
                    }
                }
                ?>
            </ul>
        </aside>

        <div class="stok-barang-page">
            <div class="product-grid">
                <?php
                if ($result_products && $result_products->num_rows > 0) {
                    while($row = $result_products->fetch_assoc()) {
                        $image_filename = htmlspecialchars($row['image']);
                        $final_image_path = "../assets/img/produk/" . $image_filename;
                        
                        // Fallback image jika file tidak ditemukan
                        if (empty($image_filename) || !file_exists($final_image_path)) {
                            $final_image_path = "https://placehold.co/200x150/EEEEEE/333333?text=Image+Not+Found";
                        }
                        ?>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <img src="<?php echo $final_image_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="category-text">Kategori: <?php echo htmlspecialchars($row['category_name']); ?></p>
                                <div class="price-section">
                                    <?php if ($row['is_promo'] && $row['promo_price'] !== null) { ?>
                                        <span class="original-price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                                        <span class="promo-price">Rp <?php echo number_format($row['promo_price'], 0, ',', '.'); ?></span>
                                    <?php } else { ?>
                                        <span class="normal-price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                                    <?php } ?>
                                </div>
                                <p class="stock-text">Stok: <?php echo htmlspecialchars($row['stock']); ?></p>
                                <p>Status: <?php echo ($row['is_promo'] ? '<span class="badge badge-warning">Promo</span>' : '<span class="badge badge-normal">Normal</span>'); ?></p>
                            </div>
                            <div class="product-actions">
                                <a href="edit_barang.php?id=<?php echo $row['id']; ?>" class="btn-action edit">Edit</a>
                                <a href="#" class="btn-action delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['id']; ?>">Hapus</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p style='text-align:center; width:100%;'>Tidak ada data barang.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</main>

<!-- Delete Modal -->
<div class="modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus barang ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="#" id="confirmDeleteButton" class="btn btn-danger">Hapus</a>
      </div>
    </div>
  </div>
</div>

<script>
    // Script untuk menangani modal hapus
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteModal');
        // Pastikan modal ada sebelum menambahkan event listener
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-id');
                const confirmDeleteButton = deleteModal.querySelector('#confirmDeleteButton');
                confirmDeleteButton.href = '../proses/proses_hapus_barang.php?id=' + productId;
            });
        }
    });
</script>

<style>
    /* CSS Khusus untuk halaman Stok Barang */
    .content-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .category-sidebar {
        flex: 0 0 250px;
        padding: 20px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        height: fit-content;
    }

    .category-sidebar h4 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
        font-weight: 600;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-item {
        margin-bottom: 5px;
    }

    .category-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-radius: 8px;
        color: #555;
        text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .category-link:hover,
    .category-link.active {
        background-color: #007bff;
        color: #fff;
    }

    .category-count {
        background-color: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .category-link.active .category-count {
        background-color: #fff;
        color: #007bff;
    }

    .stok-barang-page {
        flex: 1;
    }

    /* Product Grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
    }

    /* Product Card */
    .product-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .product-image-wrapper {
        width: 100%;
        height: 200px;
        overflow: hidden;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.1);
    }
    
    .product-info {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .product-info h5 {
        margin-top: 0;
        margin-bottom: 10px;
        font-weight: 600;
        color: #333;
    }

    .product-info p {
        margin: 0 0 8px 0;
        font-size: 0.9rem;
        color: #666;
    }
    
    .price-section {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .original-price {
        color: #888;
        text-decoration: line-through;
        margin-right: 10px;
    }

    .promo-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #dc3545;
    }
    
    .normal-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #28a745;
    }
    
    .badge {
        display: inline-block;
        padding: .35em .65em;
        font-size: .75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25rem;
    }
    .badge-warning {
        color: #212529;
        background-color: #ffc107;
    }
    .badge-normal {
        color: #fff;
        background-color: #28a745;
    }
    
    .product-actions {
        padding: 10px 20px 20px;
        display: flex;
        gap: 10px;
    }

    /* Action buttons */
    .btn-action.edit {
        background-color: #17a2b8;
        color: #fff;
    }

    .btn-action.delete {
        background-color: #dc3545;
        color: #fff;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 12px;
        border: none;
    }
    .modal-header {
        border-bottom: 1px solid #e9ecef;
    }
    .modal-footer {
        border-top: 1px solid #e9ecef;
    }
    
    /* Media Queries for Responsiveness */
    @media (max-width: 992px) {
        .content-wrapper {
            flex-direction: column;
        }
        .category-sidebar {
            flex: 1;
            margin-bottom: 20px;
            box-shadow: none;
            border-bottom: 1px solid #e0e0e0;
        }
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
</style>

<?php include '../includes/footer.php'; ?>
