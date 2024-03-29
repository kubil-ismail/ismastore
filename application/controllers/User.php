<?php 

class User extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        // Check if not login
        if ( ! $this->session->userdata('logged_in'))
        { 
            redirect(base_url('home/login'));
        }
        $this->load->model('user_models');

    }

    // Logged home
    public function index()
    {
        // DATA PAGE INDEX
        $data['title'] = "Home | Ismastore";
        $data['kategori'] = $this->user_models->getKategori();
        $data['barang'] = $this->user_models->getBarang();
        $data['user_info'] = $this->user_models->getUserInfo($this->session->userdata('id_user'));
        
        // Load Views
        $this->load->view('layouts/header.php',$data);
        $this->load->view('home/index.php',$data);
        $this->load->view('layouts/footer.php');
    }

    // Direct chat wa
    public function chat($barang)
    {   
        redirect('https://wa.me/6289630080545?text=Hai%20'.$barang.'%20ini%20ready%20gk%20');
    }

    // Manage page 
    public function manage()
    {
        // Data for this page
        $data['title'] = "Manage | Ismastore";
        $data['user_info'] = $this->user_models->getUserInfo($this->session->userdata('id_user'));
        $data['barang'] = $this->user_models->getBarang();
        $data['kategori'] = $this->user_models->getKategori();

        // Load Views
        $this->load->view('layouts/header.php',$data);
        $this->load->view('manage/index.php',$data);
        $this->load->view('layouts/footer.php');
    }

    // delete barang method 
    public function delete($id)
    {
        $this->user_models->deleteBarang($id);
        redirect(base_url('user/manage'));
    }

    // View my Profile
    public function profile()
    {
        // Data for this page
        $data['title'] = "Profile | Ismastore";
        $data['user_info'] = $this->user_models->getUserInfo($this->session->userdata('id_user'));
        $data['address'] = $this->user_models->getAddressUser($this->session->userdata('id_user'));
        $data['getProv'] = $this->user_models->getProv();
        $data['getKota'] = $this->user_models->getKota();
        $data['pending'] = $this->user_models->getPendingOrder();
        
        // Load Views
        $this->load->view('layouts/header.php',$data);
        $this->load->view('user/profile.php',$data);
        $this->load->view('layouts/footer.php');
    }

    // Add wishlist
    public function addwishlist($barang,$id)
    {   
        $this->user_models->addwishlist($barang,$id);
        echo "<script>
                alert('Wishlist berhasil di tambahkan');
                document.location.href='".base_url('views/produk/'.$id)."';
            </script>";
    }

    // Wishlist page
    public function wishlist()
    {
        // Data for this page
        $data['title'] = "Wishlist | Ismastore";
        $data['wishlist'] = $this->user_models->getWishlist($this->session->userdata('id_user'));
        $data['user_info'] = $this->user_models->getUserInfo($this->session->userdata('id_user'));
    
        // Load Views
        $this->load->view('layouts/header.php',$data);
        $this->load->view('user/wishlist.php',$data);
        $this->load->view('layouts/footer.php');
    }

    // Delete wishlist
    public function deletewishlist($id)
    {
        $this->user_models->deletewishlist($id);
        redirect(base_url('user/wishlist'));
    }

    // Page cart
    public function cart()
    {
        // Data for this page
        $data['title'] = "Wishlist | Ismastore";
        $data['user_info'] = $this->user_models->getUserInfo($this->session->userdata('id_user'));
        $data['cart'] = $this->user_models->getCart($this->session->userdata('id_user'));
        $data['price'] = 0;
        $data['address'] = $this->user_models->getAddressUser($this->session->userdata('id_user'));
        $data['getProv'] = $this->user_models->getProv();
        $data['getKota'] = $this->user_models->getKota();

        // Load Views
        $this->load->view('layouts/header.php',$data);
        $this->load->view('user/cart.php',$data);
        $this->load->view('layouts/footer.php');
    }

    // Delete cart
    public function deletecart($id)
    {
        $this->user_models->deletecart($id);
        redirect(base_url('user/cart'));
    }

    // add Cart
    public function addcart($id)
    {
        $this->user_models->addcart($id);
        echo "<script>
                alert('Berhasil di tambahkan ke keranjang');
                document.location.href='".base_url('views/produk/'.$id)."';
            </script>";
    }

    // INsert barang into database
    public function addBarang()
    {
        if ($this->session->userdata('role_id') != null || $this->input->post() == null) {
            redirect(base_url());
            exit;
        }
        
         // Data File27768
        $upload_dir = 'assets/img/barang/';
        $temp = explode(".", $_FILES["gambar"]["name"]);
        $newfilename = round(microtime(true)) . '.' . end($temp);
        $target = basename($newfilename);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "$upload_dir/$target");
        
        $input['file'] = $newfilename;
        $input['nama_barang'] = $this->input->post('nama_barang',true);
        $input['kategori_barang'] = $this->input->post('kategori_barang',true);
        $input['harga_barang'] = $this->input->post('harga_barang',true);
        $input['desc_barang'] = $this->input->post('desc_barang',true);
        $input['kategori_barang'] = $this->input->post('kategori_barang',true);

        $this->user_models->addBarang($input);
        echo "<script>
                alert('Berhasil di tambahkan');
                document.location.href='".base_url('user/manage')."';
            </script>";
    }

    // Add alamat user
    public function addAlamat()
    {
        $input['id_provinsi'] = $this->input->post('provinsi');
        $input['id_kota'] = $this->input->post('kota');
        $input['kecamatan'] = $this->input->post('kecamatan');
        $input['zipkode'] = $this->input->post('zipkode');
        $input['nama_penerima'] = $this->input->post('acepter');
        $input['alamat'] = $this->input->post('alamat');

        $this->user_models->addAdress($input);

        echo "<script>
                alert('Berhasil menambahkan alamat');
                document.location.href='".base_url('user/profile')."';
            </script>";
    }

    // Checkout payment
    public function checkout()
    {
        $this->user_models->checkout($this->input->post());
        echo "<script>
                alert('Berhasil memesan barang. Silahkan tunggu kurir kami yang akan datang pada lokasi anda dalam beberapa hari');
                document.location.href='".base_url('user/profile')."';
            </script>";
    }
}