$file = 'c:/Users/yabla/Downloads/DEV/Support_Manager/.claude/song/finish.mp3'
$bytes = [System.IO.File]::ReadAllBytes($file)
Write-Host "Size: $($bytes.Length) bytes"
$header = [System.Text.Encoding]::ASCII.GetString($bytes[0..3])
Write-Host "Header ASCII: $header"
$hex = ($bytes[0..3] | ForEach-Object { $_.ToString('X2') }) -join ' '
Write-Host "Header Hex: $hex"
