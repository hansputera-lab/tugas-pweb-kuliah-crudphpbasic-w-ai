; HRIS Installer for Windows — Inno Setup Script
; Build with Inno Setup 6.x (https://jrsoftware.org/isinfo.php)
;
; Before building:
;   1. Download portable runtimes (see README.md for download links)
;   2. Extract them into the bundle/ folder
;   3. Place the pre-built HRIS app into bundle/hris/
;   4. Open this .iss in Inno Setup and compile

#define MyAppName "HRIS"
#define MyAppVersion "1.0"
#define MyAppPublisher "HRIS Team"
#define MyAppURL "http://localhost"
#define MyAppExeName "start.bat"

[Setup]
AppId={{B8F4A3D2-1C5E-4A7B-9D6F-2E3C8A1B5D7F}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
DefaultDirName=C:\HRIS
DefaultGroupName={#MyAppName}
AllowNoIcons=yes
OutputDir=.\output
OutputBaseFilename=HRIS-Setup-{#MyAppVersion}
Compression=lzma2/max
SolidCompression=yes
WizardStyle=modern
PrivilegesRequired=admin
DisableProgramGroupPage=yes
CloseApplications=no
SetupLogging=yes

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"
Name: "indonesian"; MessagesFile: "Indonesian.isl"

[Types]
Name: "full"; Description: "Full installation (Apache + PHP + MariaDB + HRIS)"
Name: "custom"; Description: "Custom installation"; Flags: iscustom

[Components]
Name: "apache"; Description: "Apache Web Server (portable)"; Types: full custom; Flags: fixed
Name: "php"; Description: "PHP 8.2 (portable)"; Types: full custom; Flags: fixed
Name: "mariadb"; Description: "MariaDB 10.4 (portable)"; Types: full custom; Flags: fixed
Name: "hris"; Description: "HRIS Application"; Types: full custom; Flags: fixed

[Files]
; Apache portable runtime
Source: "bundle\apache\*"; DestDir: "{app}\apache"; Flags: ignoreversion recursesubdirs createallsubdirs; Components: apache

; PHP portable runtime
Source: "bundle\php\*"; DestDir: "{app}\php"; Flags: ignoreversion recursesubdirs createallsubdirs; Components: php

; MariaDB portable runtime
Source: "bundle\mariadb\*"; DestDir: "{app}\mariadb"; Flags: ignoreversion recursesubdirs createallsubdirs; Components: mariadb

; HRIS application (pre-built)
Source: "bundle\hris\*"; DestDir: "{app}\hris"; Flags: ignoreversion recursesubdirs createallsubdirs; Components: hris

; Scripts and utilities
Source: "bundle\post-install.bat"; DestDir: "{app}"; Flags: ignoreversion
Source: "bundle\start.bat"; DestDir: "{app}"; Flags: ignoreversion
Source: "bundle\stop.bat"; DestDir: "{app}"; Flags: ignoreversion
Source: "bundle\hris.ico"; DestDir: "{app}"; Flags: ignoreversion skipifsourcedoesntexist

[Icons]
Name: "{group}\Start HRIS"; Filename: "{app}\start.bat"; WorkingDir: "{app}"; IconFilename: "{app}\hris.ico"
Name: "{group}\Stop HRIS"; Filename: "{app}\stop.bat"; WorkingDir: "{app}"; IconFilename: "{app}\hris.ico"
Name: "{group}\Open HRIS"; Filename: "http://localhost"
Name: "{group}\Uninstall HRIS"; Filename: "{uninstallexe}"
Name: "{commondesktop}\HRIS"; Filename: "http://localhost"

[Run]
Filename: "{app}\post-install.bat"; Parameters: """{app}"""; WorkingDir: "{app}"; StatusMsg: "Configuring HRIS... (this may take a minute)"; Flags: runhidden
Filename: "http://localhost"; Description: "Launch HRIS"; Flags: postinstall nowait skipifsilent shellexec

[UninstallRun]
Filename: "{app}\stop.bat"; WorkingDir: "{app}"; Flags: runhidden

[Code]

var
  InfoPage: TOutputProgressWizardPage;

procedure InitializeWizard;
begin
  InfoPage := CreateOutputProgressPage('Installing', 'Setting up HRIS components...');
end;

procedure CurStepChanged(CurStep: TSetupStep);
begin
  if CurStep = ssInstall then
  begin
    InfoPage.Show;
    InfoPage.SetText('Copying files...', '');
  end;
  if CurStep = ssDone then
  begin
    InfoPage.Hide;
  end;
end;


