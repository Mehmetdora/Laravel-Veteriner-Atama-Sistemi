
# Evrak Atama - Veteriner İş Yükü Takip Sistemi


- **CANLI ORTAMDA TEST EDİLMEKTE-KULLANIMDADIR.** 🚀
- Bu projemde Tarım Ve Hayvancılık Bakanlığı'nın Mersin il müdürlüğündeki çalışanları için tüm süreçlerini kendimin geliştirdiği bir **evrak atama ve veteriner iş yükü takibi sistemidir**.
- **Bu projenin amacı kurumdaki veterinerlerin farklı katsayılardaki iş yükü olan evrakların veterinerlere random bir şekilde , aynı zamanda yıl sonunda aralarındaki toplam aldıkları evrakların iş yükleri toplamlarının aralarındaki farkları en aza indirmesi amacıyla geliştirilmiştir.** 
- Bu özelliğe ek olarak veterinerlerin aldıkları izin boyunca iş yapamamaları nedeniyle diğer veterinerlerden iş yükü bakımından geride kalmaları ve her veterinerin yıl boyunca aynı gün sayısında izin kullanmaması nedeniyle veterinerlerin izinlerinden sonraki bir süre boyunca fazladan telafi işleri atanarak(Diğer veterinerler de gözetilerek dengeli bir dağılımla) aralarındaki farkın kapatılması sağlandı.




## Genel Özellikler

- Sisteme veteriner/memur ekleme-çıkarma-düzenleme
- 3 farklı rol(veteriner hekim,memur,admin) ile kullanıcıların yetkilendirilmesi
- Admin tarafından tüm sisteme erişim sağlanabilmesi,görüntülenebilmesi
- Veteriner ve memurların takvim üzerinden izinler ve nöbetlerin eklenebilmesi(izin ve nöbet durumlarına göre evrak atanma kontrolleri yapılır)
- Evraklarda kullanılan genel verilerin dinamik olması ve admin tarafından düzenlenebilmesi
- Aynı anda birden fazla evrak kaydı
- Sağlık sertifikalarının takibi 
- Antrepoların stok durumlarının takibi(antrepolara giriş-çıkış yapan malların otomatik stok takibi ile güncellenmesi)
- Yeni yıla girildiğinde, yeni veteriner eklenmesinde-silinmesinde ve izinlerde veterinerlerin iş yüklerinin güncellenmesi sağlandı

## Yazılımsal Özellikler

- Sistem **PHP (8.2)** ile geliştirilmiş olup, **Laravel (11.3)** framework’ü kullanılmıştır.
- Veritabanı olarak **MySQL** tercih edilmiştir.
- Verilerin büyük ölçüde ilişkisel olması nedeniyle tablolar arasında **One to One, One to Many, Many to Many** ve bu ilişkilerin **polimorfik** versiyonları kullanılmıştır.
- Kod tekrarını engellemek için **Service Class** yapıları ile modüler yapı oluşturulmuştur.
- Arayüz tasarımı için **AdminLTE** ve **Bootstrap** tercih edilerek sade, yönetilebilir ve responsive bir görünüm sağlanmıştır.
- Yetkilendirme sistemi **Laravel Gate ve Policy** mekanizmalarıyla oluşturulmuştur.
- **Middleware** kullanılarak farklı kullanıcı rollerine özel erişim kontrolleri uygulanmıştır.
- **Validation** işlemleri hem Controller seviyesinde hemde view sayfalarında sağlanmıştır.
- **Exception Handling** özelleştirilerek kullanıcı dostu hata mesajları sunulmuş, sistemde oluşan hatalar loglanarak geliştirici takibi kolaylaştırılmıştır.
- Kritik işlemlerde (evrak atama, izin girişi, stok güncelleme vb.) veri tutarlılığını sağlamak amacıyla **database transaction** mekanizması kullanılmış, olası hatalarda işlemler otomatik olarak geri alınmıştır (rollback).
- Otomatik yük dengeleme ve iş yükü analizlerinde özel **servis algoritmaları** geliştirilmiştir.


  
## Ekran Görüntüleri
<p align="center">
    <img width="45%" alt="Ekran Resmi 2025-05-20 23 47 49" src="https://github.com/user-attachments/assets/54f72c87-1027-47ea-85c4-a6115b53ab47" />
    <img width="45%" alt="Ekran Resmi 2025-05-20 23 47 29" src="https://github.com/user-attachments/assets/ada933df-20c0-49a3-898f-231899a46912" />
</p>
<p align="center">
    <img width="45%" alt="Ekran Resmi 2025-05-20 23 50 22" src="https://github.com/user-attachments/assets/6c245d55-1277-48dd-8ce8-44b031bfcd39" />
<img width="45%" alt="Ekran Resmi 2025-05-20 23 50 40" src="https://github.com/user-attachments/assets/fe291bd2-18f4-42dd-8087-9be918fb11ec" />

</p>
<p align="center">
    <img width="45%" alt="Ekran Resmi 2025-05-20 23 48 22" src="https://github.com/user-attachments/assets/0863f096-18e7-4bff-946b-de34d439ea64" />
<img width="45%" alt="Ekran Resmi 2025-05-20 23 50 00" src="https://github.com/user-attachments/assets/34e1d40b-438e-48da-b71a-d5fbf2fbe984" />

</p>
<p align="center">
    <img width="45%" alt="Ekran Resmi 2025-05-20 23 47 06" src="https://github.com/user-attachments/assets/13937cb3-a997-4986-b05e-23b3c6f9a8e2" />
<img width="45%" alt="Ekran Resmi 2025-05-20 23 21 34" src="https://github.com/user-attachments/assets/a6f7c69a-b1d1-4485-86fb-20171c938660" />

</p>
<p align="center">
    <img width="45%" alt="Ekran Resmi 2025-05-20 23 48 06" src="https://github.com/user-attachments/assets/bdb39163-b1ac-44b3-a691-6dc06548b042" />
<img width="45%" alt="Ekran Resmi 2025-05-20 23 48 53" src="https://github.com/user-attachments/assets/3cd54083-f048-4d7a-bb22-8edcbdbbea3a" />

</p>



  
## Geri Bildirim

Herhangi bir geri bildiriminiz varsa, lütfen mehmetdora333@gmail.com adresinden bana ulaşın.

  
