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
    <div class="header-content d-flex justify-content-between align-items-center">
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
                
                if (empty($image_filename) || !file_exists($final_image_path)) {
                    $final_image_path = "https://placehold.co/200x150/EEEEEE/333333?text=Image+Not+Found";
                }
                ?>
                <div class="product-card">
                    <img src="<?php echo $final_image_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="product-info">
                        <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p>Kategori: <?php echo htmlspecialchars($row['category_name']); ?></p>
                        <p>Harga: 
                            <?php if ($row['is_promo'] && $row['promo_price'] !== null) { ?>
                                <span class="original-price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                                <span class="promo-price">Rp <?php echo number_format($row['promo_price'], 0, ',', '.'); ?></span>
                            <?php } else { ?>
                                Rp <?php echo number_format($row['price'], 0, ',', '.'); ?>
                            <?php } ?>
                        </p>
                        <p>Stok: <?php echo htmlspecialchars($row['stock']); ?></p>
                        <p>Status: <?php echo ($row['is_promo'] ? '<span class="badge bg-warning text-dark">Promo</span>' : 'Normal'); ?></p>
                    </div>
                    <div class="product-actions">
                        <a href="../proses/proses_edit_barang.php?id=<?php echo $row['id']; ?>" class="btn-action edit">Edit</a>
            
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
    </div>
</main>

<script>
    // Script untuk menangani modal hapus
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-id');
            const confirmDeleteButton = deleteModal.querySelector('#confirmDeleteButton');
            confirmDeleteButton.href = '../proses/proses_hapus_barang.php?id=' + productId;
        });
    });
</script>

<?php include '../includes/footer.php'; ?>