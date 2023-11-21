<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Barang</title>
</head>
<link href="css/report.css" rel="stylesheet"/>
<body>
    <img src="css/logojsasik.png" class="gambar">
    <div class="header1">
        <center>
            <h1>JOAN SETYO COMPANY</h1>
            <p>Jakarta Timur, Condet Jl. Batu Ampar V No.12</p>
            <hr size="7px">
        </center>
    </div>
    <h3>LAPORAN PENDATAAN BARANG</h3>
    <br>
    <?php
    include 'function.php'
    ?>
    <div class="table">
        <table border="2" style="width:100%">
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Deskripsi</th>
                <th>Stock / Unit</th>
            </tr>
            <?php
    $i = 1;
    $sql = mysqli_query($conn, "select * from stock");
    while($data =mysqli_fetch_array($sql)){
        
        ?>
    <tr>
        <td><?=$i++;?></td>
        <td><?php echo $data['namabarang'];?></td>
        <td><?php echo $data['deskripsi'];?></td>
        <td><?php echo $data['stock'];?></td>   
    </tr>
    <?php
    }
    ?>
    </table>
    </div>
    <br>
    <div class="penanggung">
        <p>Bekasi, ............ 20....</p>
        <p>Divisi Sarana Prasarana</p>
        <br>
        <br>
        <hr>
    </div>
    <script>
        window.print()
    </script>
</body>
</html>