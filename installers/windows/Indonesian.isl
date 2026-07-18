; Inno Setup Indonesian Language File
; Contributed by Zaenal Arifin and Timotius E. Win. Saputro

[LangOptions]
LanguageID=0x0421
LanguageName=Indonesian
LanguageCodePage=1252
DialogFontName=MS Sans Serif
DialogFontSize=8
WelcomeFontName=MS Sans Serif
WelcomeFontSize=12
TitleFontName=MS Sans Serif
TitleFontSize=29
CopyrightFontName=MS Sans Serif
CopyrightFontSize=8

[Messages]
; SetupLdr
SetupLdrStartupMessage=Program instalasi akan mempersiapkan {#SetupSetting("AppName")} untuk diinstalasi.%n%nLanjutkan?
Win95NotSupportedBySupport=Program instalasi ini membutuhkan Windows 95 atau yang lebih baru.
WinNT3xNotSupportedBySupport=Program instalasi ini membutuhkan Windows NT 3.51 atau yang lebih baru.
NonAdminInstallNotSupported=Anda harus masuk (login) dengan hak administrator ketika menjalankan program instalasi ini.
NoVolumeLabel=serial volume

; File
FileError1=Program instalasi mengalami kegagalan ketika menulis berkas ke dalam folder:%n%1
FileError2=Klik Cancel untuk berhenti melakukan instalasi, atau%nRetry untuk mencoba lagi.
FileError3=Klik Abort untuk berhenti melakukan instalasi,%nRetry untuk mencoba lagi, atau%nIgnore untuk melewatkan berkas ini.
FileError4=Klik Yes untuk memindahkan berkas,%nNo untuk tidak memindahkan, atau%nCancel untuk membatalkan instalasi.
FileExistsInDirWarning=Berkas '%1' sudah ada di folder '%2'.%n%nApakah Anda ingin menimpa berkas ini?

; Misc
ConfirmUninstall=Apakah Anda yakin akan menghapus totally %1 dan semua komponennya?
UninstallStatusLabel=Menghapus %1 dari komputer Anda...
UninstallStatusLabel2=Mohon tunggu sementara %1 dihapus dari komputer Anda...
UninstallProgramNotFound=Program '%1' tidak ditemukan. Proses penghapusan akan dihentikan.
ErrorOpeningFile=Kesalahan ketika membuka berkas:%n%1
ErrorReadingFile=Kesalahan ketika membaca berkas:%n%1
ErrorWritingFile=Kesalahan ketika menulis berkas:%n%1
CannotDeleteFile=Berkas berikut tidak dapat dihapus:%n%1%n%nKlik Yes untuk menghapusnya secara langsung (paksa), atau No untuk mengabaikannya.
CancelInstall=Apakah Anda yakin akan membatalkan instalasi?
CancelInstall2=Apakah Anda yakin akan membatalkan?

; Startup
SourceDoesNotExist=Berkas sumber '%1' tidak ditemukan.
CreateFolder=Buat Folder
SelectFolderTitle=Pilih Folder Tujuan
SelectFolderLabel=Program instalasi akan menginstal %1 ke dalam folder berikut.
SelectFolderLabel2=Untuk melanjutkan, klik Next. Jika Anda ingin menginstal ke folder yang berbeda, klik Browse.
SelectFolderBrowseLabel=Untuk melanjutkan ke langkah berikutnya, klik Next.
SelectStartMenuFolderTitle=Pilih Folder di Menu Start
SelectStartMenuFolderLabel=Mohon pilih folder untuk menempatkan shortcut program.
SelectStartMenuFolderBrowse=Untuk membuat folder baru, ketika menekan tombol Browse%ndan akan membuat folder yang baru.
SelectProgramGroup=Silahkan pilih folder dari Menu Start dimana Anda ingin meletakkan shortcut program.%n%nKlik Next untuk melanjutkan.
DiskSpaceWarning=Program instalasi membutuhkan setidaknya %1 KB ruang kosong pada harddisk untuk dapat menginstal.%n%nDrive ini tidak memiliki cukup ruang kosong. Anda disarankan untuk menghentikan instalasi.
DiskSpaceWarning2=Ruang kosong yang diperlukan:%n%1 KB%nRuang kosong yang tersedia:%n%2 KB
WinVersionTooLowError=Program instalasi ini membutuhkan %1 atau yang lebih baru.
WinVersionTooLowErrorHelp=Kesalahan Versi Windows
WinVersionTooHighError=Program instalasi ini tidak dapat diinstal pada Windows versi %1.
InvalidFolderName=Folder tujuan yang Anda masukkan tidak valid.
InvalidFolderName2=Mohon masukkan nama folder yang valid.
PathTooLong=Nama folder yang dimasukkan terlalu panjang.
DirExists=Folder '%1' sudah ada.%n%nApakah Anda ingin tetap menginstal ke folder tersebut?
DirDoesntExist=Folder '%1' tidak ditemukan.%n%nApakah Anda ingin membuatnya?
AppDuringInstall=Anda harus menutup semua aplikasi sebelum menjalankan instalasi.%n%nKlik Next untuk melanjutkan.
AppRunning=Program instalasi mendeteksi bahwa %1 sedang berjalan.%n%nHarap tutup %1 terlebih dahulu, lalu klik Retry, atau klik Next untuk tetap melanjutkan instalasi.

; Prepare
ComponentsDiskSpaceWarningTitle=Ruang Harddisk Tidak Mencukupi
ComponentsDiskSpaceWarningDesc=Sub-komponen yang terpilih berikut ini tidak dapat diinstal karena ruang harddisk tidak mencukupi.%n%nHilangkan pilihan Anda pada sub-komponen tersebut, atau hentikan instalasi dan sediakan ruang harddisk yang cukup pada drive yang Anda pilih.

; Exit
ExitSetupTitle=Keluar dari Program Instalasi
ExitSetupMessage=Anda belum menyelesaikan instalasi. Jika Anda keluar sekarang, program tidak akan diinstal.%n%nKeluar sekarang?
ExitSetupMessage2=Anda telah menyelesaikan proses instalasi.%n%nTerima Kasih telah menggunakan %1.%n%nKlik Close untuk keluar.

; Setup仅在之后显示的共享/只读
SharedFileLabel=Jadikan berkas ini digunakan bersama (shared)
SharedFileLabel1=&Jadikan berkas ini digunakan bersama (shared)
SharedFileLabel2=Jadikan berkas ini digunakan bersama oleh semua pengguna
SharedFileWarning1=Berkas ini sudah ada, tetapi bukan merupakan berkas bersama. Berkas bersama menandakan bahwa berkas tersebut diinstal oleh program lain.%n%nJika Anda menghapus berkas ini, program lain mungkin tidak dapat berjalan dengan baik. Jika Anda tidak yakin bahwa program lain menggunakan berkas ini, disarankan untuk membiarkannya sebagai berkas bersama.
SharedFileWarning2=Berkas ini sudah ditandai sebagai berkas bersama. Program lain bergantung pada keberadaan berkas ini untuk dapat berjalan.%n%nPenghapusan akan menyebabkan program lain menjadi tidak dapat berjalan. Disarankan untuk tidak menghapus berkas bersama.

; Misc
CannotReadFromSourceDialog=Kesalahan ketika membaca dari sumber
FileNameLabel=Nama Berkas:
FolderLabel=Folder:
ByteLabel=B
KilobyteLabel=KB
MegabyteLabel=MB
GigabyteLabel=GB
StatusExtractFile=Mengekstrak berkas...
StatusCreateFolder=Membuat folder...
StatusCreateDirs=Membuat sub-folder...
StatusCreateShortcut=Membuat shortcut...
StatusCreateShortcuts=Membuat shortcut...
StatusInstall=Instalasi...
StatusInstallData=Memasang data...
StatusRollback=Membatalkan instalasi...
StatusRollbackData=Membatalkan data instalasi...
StatusUninstall=Penghapusan...
StatusUninstallData=Menghapus data...
StatusCleanup=Membersihkan...
PreparingDesc=Mempersiapkan untuk menginstal...
CompletedLabel=Instalasi selesai.
CompletedLabel2=Selesai
FinishedHeadingLabel=Program Instalasi telah selesai menginstal %1
FinishedLabel=Program Instalasi telah menyelesaikan instalasi %1 di komputer Anda.
FinishedLabelNoIcons=Program Instalasi telah menyelesaikan instalasi %1 di komputer Anda.
ClickFinish=Klik Close untuk keluar dari Program Instalasi.
RunEntry= Jalankan %1 ketika instalasi selesai

; Uninstall
UninstallAppTitle=Penghapusan - %1
UninstallAppFullTitle=Penghapusan %1
UninstallAppFullTitle2=Program Penghapusan %1
ConfirmUninstall=Apakah Anda yakin akan menghapus totally %1?
UninstallStatusLabel=Menghapus %1 dari komputer Anda...
UninstallStatusLabel2=Mohon tunggu sementara %1 dihapus dari komputer Anda...
UninstallProgramNotFound=Program '%1' tidak ditemukan. Proses penghapusan akan dihentikan.
UninstallCompletedLabel=Penghapusan %1 telah selesai.
UninstallCompletedLabel2=Penghapusan telah selesai.
UninstallFinishedLabel=%1 telah dihapus dari komputer Anda.
UninstallFinishedLabel2=Klik Close untuk keluar dari Program Penghapusan.

; License
LicenseLabel=Perjanjian Lisensi
LicenseLabel3=lisensi
LicenseAccepted=Saya &menyetujui perjanjian di atas
LicenseNotAccepted=Saya &tidak menyetujui perjanjian di atas

; Password
PasswordLabel1=Kata Sandi
PasswordLabel2=Kata sandi diperlukan untuk melanjutkan instalasi %1.%n%nSilakan masukkan kata sandi Anda.
PasswordEditLabel=&Kata Sandi:
PasswordInvalidLabel=Kata sandi tidak valid, silakan coba lagi.
PasswordInvalid=Kata sandi tidak valid.
PasswordPanelNext=Berikutnya
PasswordPanelPassword=Kata sandi

; InfoBefore
InfoBeforeLabel=Informasi
InfoBeforeLabelClick=

; InfoAfter
InfoAfterLabel=Informasi
InfoAfterLabelClick=

; New/Existing folder
NewFolderName=Nama folder baru:
ExistingFolderName=Folder yang sudah ada:

; Browse dialog
BrowseDialogTitle=Pilih folder tujuan
BrowseDialogLabel=Pilih folder yang akan digunakan kemudian klik OK.
BrowseDialogLabel2=Pilih folder:

; Select Start Menu folder
SelectStartMenuFolderTitle=Pilih Folder Program
SelectStartMenuFolderLabel=Pilih folder untuk membuat shortcut program Anda, kemudian klik Install.
SelectStartMenuFolderBrowse=Klik Next untuk melanjutkan.

; Groups
GroupStartMenuFolderName=

; Select Components
SelectComponentsDesc=Pilih komponen aplikasi yang ingin Anda instal.
SelectComponentsTitle=Pilih Komponen
SelectComponentsLabel=Pilih komponen yang ingin Anda instal; kosongkan yang tidak ingin diinstal.
SelectComponentsLabel2=Pilih komponen yang ingin Anda instal:%n%n
SelectComponentsLabel3=Deskripsi komponen
SelectComponentsDescription=Letakkan mouse Anda di atas komponen untuk melihat deskripsinya.
SelectComponentsSize1=Komponen yang dipilih saat ini membutuhkan setidaknya %1 MB ruang harddisk.
SelectComponentsSize2=Komponen yang dipilih saat ini membutuhkan setidaknya %1 MB ruang harddisk. Selain itu, dibutuhkan minimal %2 MB ruang harddisk.

; Setup default
SetupAppRunningError=Program instalasi mendeteksi bahwa %1 sedang berjalan.%n%nHarap tutup %1 dan klik OK, atau klik Cancel untuk keluar.
SetupAppRunningError2=Harap tutup aplikasi-aplikasi berikut:%n%n%1%n%nlalu klik OK untuk melanjutkan.

; Download
DownloadingLabel=Mengunduh...
DownloadingLabel2=%1 dari %2

; Prepish
PreviousInstallNotCompleted=Instalasi sebelumnya belum selesai. Instalasi tidak dapat dilanjutkan.
PreviousInstallAborted=Instalasi sebelumnya dibatalkan. Instalasi harus di-restart.
CannotContinue=Program tidak dapat dilanjutkan. Klik Cancel untuk keluar.

[CustomMessages]
NameAndVersion=%1 versi %2
AdditionalIcons=Icon tambahan:
CreateDesktopIcon=Icon di &desktop
CreateQuickLaunchIcon=Icon di Quick &Launch
ProgramOnTheWeb=%1 di Web
UninstallProgram=Hapus %1
LaunchProgram=Jalankan %1
AssocFileExtension=&Associate %1 dengan tipe berkas %2
AssocingFileExtension=Mengasosiasikan %1 dengan tipe berkas %2...
AlreadyAssocFileExtension=%1 sudah diasosiasikan dengan tipe berkas %2.
