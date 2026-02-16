[Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType = WindowsRuntime] | Out-Null
[Windows.Data.Xml.Dom.XmlDocument, Windows.Data.Xml.Dom.XmlDocument, ContentType = WindowsRuntime] | Out-Null

$xml = '<toast><visual><binding template="ToastGeneric"><text>CCNotify Test</text><text>Ca marche depuis Claude Code ?</text></binding></visual></toast>'
$toastXml = New-Object Windows.Data.Xml.Dom.XmlDocument
$toastXml.LoadXml($xml)
$toast = [Windows.UI.Notifications.ToastNotification]::new($toastXml)
[Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier("Claude Code").Show($toast)
