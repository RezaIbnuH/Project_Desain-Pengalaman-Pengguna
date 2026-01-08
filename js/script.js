// Inisialisasi untuk menyimpan barang dan status
window.items = window.items || [];
window.ambilItems = window.ambilItems || [];
window.editIndex = window.editIndex || -1;
window.riwayatData = window.riwayatData || []; // Misalkan data riwayat ada di sini

// ----------------------- Barang Masuk -----------------------
window.addOrUpdateItem = function () {
  const fields = ["kode", "noIdentitas", "namaPenitip", "noTelepon", "namaBarang", "tanggalMasuk", "jamMasuk", "petugas"];
  const values = fields.map(id => document.getElementById(id)?.value);
  
  // Cek apakah ada kolom yang kosong
  if (values.includes("") || values.some(value => !value)) {
    alert("Semua kolom harus diisi!");
    return;
  }

  const [kode, noIdentitas, namaPenitip, noTelepon, namaBarang, tanggalMasuk, jamMasuk, petugas] = values;

  if (window.editIndex === -1) {
    // Tambahkan barang baru
    window.items.push({ kode, noIdentitas, namaPenitip, noTelepon, namaBarang, tanggalMasuk, jamMasuk, petugas });
  } else {
    // Update barang yang sudah ada
    window.items[window.editIndex] = { kode, noIdentitas, namaPenitip, noTelepon, namaBarang, tanggalMasuk, jamMasuk, petugas };
    window.editIndex = -1;  // Reset edit index
  }

  // Clear inputs dan refresh tabel
  fields.forEach(id => document.getElementById(id)?.value = "");
  displayItems();
};

window.displayItems = function () {
  const tableBody = document.getElementById("itemTableBody");
  if (!tableBody) return;
  tableBody.innerHTML = "";  // Clear tabel sebelum menampilkan data

  window.items.forEach((item, index) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${item.kode}</td>
      <td>${item.noIdentitas}</td>
      <td>${item.namaPenitip}</td>
      <td>${item.noTelepon}</td>
      <td>${item.namaBarang}</td>
      <td>${item.tanggalMasuk}</td>
      <td>${item.jamMasuk}</td>
      <td>${item.petugas}</td>
      <td>
        <button onclick="editItem(${index})">Edit</button>
        <button onclick="deleteItem(${index})">Hapus</button>
        <button onclick="confirmAmbilBarang(${index})">Konfirmasi</button>
      </td>
    `;
    tableBody.appendChild(row);
  });
};

window.editItem = function (index) {
  const item = window.items[index];
  if (!item) return;
  const fields = ["kode", "noIdentitas", "namaPenitip", "noTelepon", "namaBarang", "tanggalMasuk", "jamMasuk", "petugas"];
  fields.forEach(id => document.getElementById(id).value = item[id]);
  window.editIndex = index;
};

window.deleteItem = function (index) {
  if (confirm("Apakah Anda yakin ingin menghapus barang ini?")) {
    window.items.splice(index, 1);  // Hapus barang dari array
    displayItems();  // Refresh tabel
  }
};

window.toggleForm = function () {
  const form = document.querySelector(".form-penambahan");
  if (!form) return;
  form.classList.toggle("hidden");  // Toggle kelas 'hidden' untuk menyembunyikan atau menampilkan form
};

// ----------------------- Barang Keluar -----------------------
window.searchBarang = function () {
  const input = document.getElementById("search-kode");
  if (!input) return;
  const kode = input.value;
  const barang = window.items.find((item) => item.kode === kode);

  if (barang) {
    window.currentBarang = barang;
    const info = document.getElementById("barang-info");
    if (info) info.style.display = "block";
    Object.keys(barang).forEach((key) => {
      const el = document.getElementById(key);
      if (el) el.innerText = barang[key];
    });
  } else {
    alert("Barang tidak ditemukan!");
  }
};

window.confirmAmbilBarang = function (index) {
  const barang = window.items[index];
  const namaPengambil = prompt("Masukkan Nama Pengambil: ");
  if (!namaPengambil) return alert("Nama Pengambil harus diisi!");

  const tanggalAmbil = document.getElementById("tanggal-ambil").value;
  const jamAmbil = document.getElementById("jam-ambil").value;
  const petugasAmbil = document.getElementById("petugas-ambil").value;
  
  if (!tanggalAmbil || !jamAmbil || !petugasAmbil) {
    alert("Semua kolom harus diisi!");
    return;
  }

  window.ambilItems.push({
    ...barang,
    namaPengambil,
    tanggalAmbil,
    jamAmbil,
    petugas: petugasAmbil,
    status: "Diambil",
  });

  displayAmbilItems();
  closeOverlay();
};

window.displayAmbilItems = function () {
  const tableBody = document.getElementById("ambilTableBody");
  if (!tableBody) return;
  tableBody.innerHTML = "";  // Clear tabel

  window.ambilItems.forEach((item, index) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${item.kode}</td>
      <td>${item.namaPenitip}</td>
      <td>${item.namaBarang}</td>
      <td>${item.tanggalAmbil}</td>
      <td>${item.jamAmbil}</td>
      <td>${item.petugas}</td>
      <td>${item.status}</td>
    `;
    tableBody.appendChild(row);
  });
};

// ----------------------- Riwayat -----------------------
window.displayData = function (data) {
  const tableBody = document.getElementById("riwayatTableBody");
  if (!tableBody) return;
  tableBody.innerHTML = "";

  data.forEach((item, index) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${item.kode}</td>
      <td>${item.namaPengambil}</td>
      <td>${item.namaPenitip}</td>
      <td>${item.namaBarang}</td>
      <td>${item.tanggalMasuk}</td>
      <td>${item.jamMasuk}</td>
      <td>${item.petugas}</td>
      <td>${item.status}</td>
    `;
    tableBody.appendChild(row);
  });
};

window.filterData = function () {
  const searchBox = document.getElementById("search-box")?.value.toLowerCase();
  const filteredData = window.riwayatData.filter((item) => 
    Object.values(item).some(val => val.toLowerCase().includes(searchBox))
  );

  displayData(filteredData);
};

// Auto-run display for pages if present
(function initPages() {
  if (document.getElementById("riwayatTableBody")) displayData(window.riwayatData);
  if (document.getElementById("itemTableBody")) displayItems();
  if (document.getElementById("ambilTableBody")) displayAmbilItems();
})();

// ----------------------- Profil Petugas -----------------------
window.saveData = function () {
  const nama = document.getElementById("new-nama")?.value;
  const email = document.getElementById("new-email")?.value;
  const telepon = document.getElementById("new-telepon")?.value;
  const alamat = document.getElementById("new-alamat")?.value;
  
  if (!nama || !email || !telepon || !alamat) {
    alert("Semua kolom harus diisi!");
    return;
  }

  alert("Data berhasil disimpan");
  closeOverlayProfile("data");
};

window.changePassword = function () {
  const pwd = document.getElementById("new-password")?.value;
  const confirm = document.getElementById("confirm-password")?.value;
  
  if (pwd !== confirm) {
    alert("Password tidak cocok");
    return;
  }

  alert("Password berhasil diganti");
  closeOverlayProfile("password");
};

// Overlay untuk profil petugas
window.openOverlayProfile = function (type) {
  const overlayData = document.getElementById("overlay-data");
  const overlayPassword = document.getElementById("overlay-password");
  
  if (type === "data") {
    overlayData?.style.display = "flex";
    overlayPassword?.style.display = "none";
  } else if (type === "password") {
    overlayPassword?.style.display = "flex";
    overlayData?.style.display = "none";
  }
};

window.closeOverlayProfile = function (type) {
  const overlay = type === "data" ? document.getElementById("overlay-data") : document.getElementById("overlay-password");
  overlay?.style.display = "none";
};
