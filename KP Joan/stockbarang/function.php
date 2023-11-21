<?php

session_start();

//connect ke database
$conn = mysqli_connect("localhost", "root", "", "stockbarang");

//menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    //gambar
    $allowed_extension = array('png','jpg','jpeg');
    $nama = $_FILES['file']['name']; //ambil gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //ambil ekstensinya
    $ukuran = $_FILES['file']['size']; //ambil size
    $file_tmp = $_FILES['file']['tmp_name']; //ambil lokasi file

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi; //mengabungkan nama filenyang di enkripsi dgn ekstensinya

    //validisasi udah ada atau belum
    $cek = mysqli_query($conn, "select * from stock where namabarang='$namabarang'");
    $hitung = mysqli_num_rows($cek);

    if($stock>=1){

        if($hitung<1){
            //jika belum ada
            
            //proses uploud gambar
            if (in_array($ekstensi, $allowed_extension) === true){
                //validasi ukuran filenya
                if($ukuran < 15000000){
                    move_uploaded_file($file_tmp, 'image/'.$image);
    
                    $addtotable = mysqli_query($conn, "insert into stock (namabarang, deskripsi, stock, image) values('$namabarang','$deskripsi','$stock','$image')");
                    if ($addtotable) {
                        header('location:index.php');
                    } else {
                        echo 'Gagal!';
                        header('location:index.php');
                    }
                } else {
                    //jika file > 1.5mb
                    echo "<script>alert('Ukuran file terlalu besar');document.location.href='index.php'</script>";
                }
            } else {
                //jika bukan jpg / png
                echo "<script>alert('File harus berformat jpg/png');document.location.href='index.php'</script>";
            }
        } else {
            //jika sudah ada
            echo "<script>alert('Nama barang sudah terdaftar');document.location.href='index.php'</script>";
        }
    } else{
        echo "<script>alert('Data barang harus > 0');document.location.href='index.php'</script>";
    }
}


//Menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildata = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildata['stock'];
    $stockandquantity = $stocksekarang + $qty;

    $addtomasuk = mysqli_query($conn, "insert into masuk (idbarang, keterangan, qty) values('$barangnya','$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock ='$stockandquantity' where idbarang='$barangnya' ");
    if ($addtomasuk && $updatestockmasuk) {
        header('location:masuk.php');
    } else {
        echo 'Gagal!';
        header('location:masuk.php');
    }
}

//Menambah barang keluar
if (isset($_POST['barangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildata = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildata['stock'];

    if($stocksekarang >= $qty){
        //Jika mencukupi
        $stockandquantity = $stocksekarang - $qty;

        $addtokeluar = mysqli_query($conn, "insert into keluar (idbarang, penerima, qty) values('$barangnya','$penerima', '$qty')");
        $updatestockmasuk = mysqli_query($conn, "update stock set stock ='$stockandquantity' where idbarang='$barangnya' ");
        if ($addtokeluar && $updatestockmasuk) {
            header('location:keluar.php');
        } else {
            echo 'Gagal!';
            header('location:keluar.php');
        }
    } else {
        //jika tidak mencukupi
        echo "<script>alert('Stock Saat Ini Tidak Mencukupi'); document.location.href='keluar.php';</script>";
    }
}

//Update Info Barang
if (isset($_POST['updatebarang'])){
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    //gambar
    $allowed_extension = array('png','jpg','jpeg');
    $nama = $_FILES['file']['name']; //ambil gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //ambil ekstensinya
    $ukuran = $_FILES['file']['size']; //ambil size
    $file_tmp = $_FILES['file']['tmp_name']; //ambil lokasi file

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi; //mengabungkan nama file ang di enkripsi dgn ekstensinya

    if($ukuran==0){
        //jika tidak ingin uploud
        $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang='$idb'");
        if ($update) {
            header('location:index.php');
        } else {
            echo 'Gagal!';
            header('location:index.php');
        }
    } else {
        //jika iya
        move_uploaded_file($file_tmp,'image/'.$image);
        $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi', image='$image' where idbarang='$idb'");
        if ($update) {
            header('location:index.php');
        } else {
            echo 'Gagal!';
            header('location:index.php');
        }
    }
}

//Delete Barang
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb']; //idbarang

    $gambar = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $get = mysqli_fetch_array($gambar); 
    $img = 'image/'.$get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "delete from stock where idbarang='$idb'");
    if ($hapus) {
        header('location:index.php');
    } else {
        echo 'Gagal!';
        header('location:index.php');
    }
}

//Megubah data barang masuk
if (isset($_POST['updatebarangmasuk'])) {
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from masuk where idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if ($qty > $qtyskrg) {
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kurangistock = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk = '$idm'");
        if ($kurangistock && $updatenya) {
            header('location:masuk.php');
        } else {
            echo 'Gagal!';
            header('location:masuk.php');
        }
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg - $selisih;
        $kurangistock = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
        if ($kurangistock && $updatenya) {
            header('location:masuk.php');
        } else {
            echo 'Gagal!';
            header('location:masuk.php');
        }
    }
}
    //Menghapus barang masuk
    if (isset($_POST['hapusbarangmasuk'])){
        $idb = $_POST['idb'];
        $qty = $_POST['kty'];
        $idm = $_POST['idm'];

        $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $data = mysqli_fetch_array($getdatastock);
        $stok = $data['stock'];

        $selisih = $stok-$qty;

        $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
        $hapusdata = mysqli_query($conn,"delete from masuk where idmasuk='$idm'");

        if ($update&&$hapusdata) {
            header('location:masuk.php');
        } else {
            header('location:masuk.php');
        }
}

//Megubah data barang keluar
if (isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from keluar where idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];
    if($stockskrg >= $qty){

        if ($qty > $qtyskrg) {
            $selisih = $qty - $qtyskrg;
            $kurangin = $stockskrg - $selisih;
            $kurangistock = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang = '$idb'");
            $updatenya = mysqli_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar = '$idk'");
            if ($kurangistock && $updatenya) {
                header('location:keluar.php');
            } else {
                echo 'Gagal!';
                header('location:keluar.php');
            }
        } else {
            $selisih = $qtyskrg - $qty;
            $kurangin = $stockskrg + $selisih;
            $kurangistock = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
            if ($kurangistock && $updatenya) {
                header('location:keluar.php');
            } else {
                echo 'Gagal!';
                header('location:keluar.php');
            }
        }
    } else {
        echo "<script>alert('Stock Saat Ini Tidak Mencukupi'); document.location.href='keluar.php';</script>";
    }
}
    //Menghapus barang keluar
    if (isset($_POST['hapusbarangkeluar'])){
        $idb = $_POST['idb'];
        $qty = $_POST['kty'];
        $idk = $_POST['idk'];

        $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $data = mysqli_fetch_array($getdatastock);
        $stok = $data['stock'];

        $selisih = $stok-$qty;

        $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
        $hapusdata = mysqli_query($conn,"delete from keluar where idkeluar='$idk'");

        if ($update&&$hapusdata) {
            header('location:keluar.php');
        } else {
            header('location:keluar.php');
        }
}