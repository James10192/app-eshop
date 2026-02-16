param([string]$SoundFile)
$wmp = New-Object -ComObject WMPlayer.OCX
$wmp.settings.volume = 100
$wmp.URL = $SoundFile
$wmp.controls.play()
# Attendre que le fichier soit chargé et joué
Start-Sleep -Seconds 1
while ($wmp.playState -eq 6) { Start-Sleep -Milliseconds 200 }  # 6 = Transitioning
while ($wmp.playState -eq 3) { Start-Sleep -Milliseconds 200 }  # 3 = Playing
$wmp.close()
