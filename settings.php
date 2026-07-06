<?php

declare(strict_types=1);

/**
 * 设置项类型枚举
 */
enum SettingType: string
{
    case Toggle = 'toggle';
    case Info   = 'info';

    public function label(): string
    {
        return match ($this) {
            self::Toggle => '开关',
            self::Info   => '信息',
        };
    }
}

/**
 * 设置项配置：使用 readonly 类，不可变
 */
readonly class SettingItem
{
    public function __construct(
        public string $label,
        public string $description,
        public SettingType $type,
        public string $value,
        public ?string $jsAction = null,
        public ?string $jsSwitchId = null,
    ) {
    }
}

/**
 * 页面配置
 */
readonly class PageConfig
{
    public function __construct(
        public string $title,
        public string $studioName,
        public string $foundedDate,
        public string $version,
        public string $primaryColor,
        public string $homeUrl,
    ) {
    }
}

$config = new PageConfig(
    title: '设置 - HAGo Studio',
    studioName: 'HAGo Studio',
    foundedDate: '2023年10月15日',
    version: '1.0.0',
    primaryColor: '#3aa6a6',
    homeUrl: 'index.php',
);

/**
 * 使用 PHP 数组 + 命名参数组织设置项
 */
$settings = [
    new SettingItem(
        label: '深色模式',
        description: '切换深色/浅色主题',
        type: SettingType::Toggle,
        value: '',
        jsAction: 'toggleDarkMode()',
        jsSwitchId: 'darkModeSwitch',
    ),
    new SettingItem(
        label: '高级材质',
        description: '开启后弹窗和菜单背景会有模糊效果',
        type: SettingType::Toggle,
        value: '',
        jsAction: 'toggleAdvancedMaterials()',
        jsSwitchId: 'advancedMaterialsSwitch',
    ),
    new SettingItem(
        label: '主题颜色',
        description: '当前主题：HAGo青蓝',
        type: SettingType::Info,
        value: '#3aa6a6',
    ),
    new SettingItem(
        label: '版本信息',
        description: 'HAGo Studio v' . $config->version,
        type: SettingType::Info,
        value: $config->version,
    ),
];

/**
 * 侧边导航
 */
$navItems = [
    ['href' => $config->homeUrl, 'label' => '首页'],
    ['href' => 'settings.php', 'label' => '设置', 'active' => true],
];

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

?><!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light dark">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)">
<meta name="darkreader-lock">
<title><?= e($config->title) ?></title>
<link rel="stylesheet" href="styles.css">

<style>
/* 设置页特有样式：设置项、开关、返回按钮 */

.setting-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid #eee;
  transition: border-color 0.3s;
}

body.dark-mode .setting-item {
  border-color: #333;
}

.setting-item:last-child {
  border-bottom: none;
}

.setting-item .setting-label {
  font-size: 16px;
  color: #333;
  transition: color 0.3s;
}

body.dark-mode .setting-item .setting-label {
  color: #fff;
}

.setting-item .setting-desc {
  font-size: 12px;
  color: #888;
  margin-top: 4px;
  transition: color 0.3s;
}

body.dark-mode .setting-item .setting-desc {
  color: #666;
}

/* 开关 */
.toggle-switch {
  position: relative;
  width: 50px;
  height: 28px;
  background: #ccc;
  cursor: pointer;
  border-radius: 0;
  transition: background 0.3s;
}

.toggle-switch.on {
  background: #3aa6a6;
}

.toggle-switch .toggle-slider {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 24px;
  height: 24px;
  background: white;
  border-radius: 0;
  transition: transform 0.3s;
}

.toggle-switch.on .toggle-slider {
  transform: translateX(22px);
}

/* 返回按钮 */
.back-btn {
  padding: 10px 20px;
  background: #3aa6a6;
  color: white;
  border: none;
  border-radius: 0;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.3s;
  margin-bottom: 20px;
}

.back-btn:hover {
  background: #2d8a8a;
}
</style>
</head>
<body>

<!-- 侧滑菜单遮罩 -->
<div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- 侧滑菜单 -->
<div id="sidebar" class="sidebar">
  <ul class="sidebar-menu">
    <?php foreach ($navItems as $item): ?>
      <li>
        <a href="<?= e($item['href']) ?>"
          <?php if (!empty($item['active'])): ?>class="active"<?php endif; ?>>
          <?= e($item['label']) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="sidebar-footer">
    <p><?= e($config->studioName) ?></p>
    <p>成立于<?= e($config->foundedDate) ?></p>
    <p>专注游戏创作</p>
  </div>
</div>

<header>
  <div class="header-left" onclick="toggleSidebar()">
    <svg width="35" height="35" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M10.8 1.8H12.6V3.6H14.4V5.4H16.2V7.2H18V9H19.8V10.8H21.6V12.6H23.4V14.4H25.2V16.2H27V25.2H25.2V27H1.8V25.2H0V1.8H1.8V0H10.8V1.8ZM5.4 21.6H21.6V19.8H19.8V18H18V16.2H16.2V14.4H14.4V12.6H12.6V10.8H10.8V9H9V7.2H7.2V5.4H5.4V21.6ZM25.2 1.8H27V9H25.2V7.2H23.4V5.4H21.6V3.6H19.8V1.8H18V0H25.2V1.8Z" fill="#48BDC6"/>
    </svg>
  </div>
  <div class="header-center">
    <svg class="header-logo-text" width="116" height="27" viewBox="0 0 116 27" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g clip-path="url(#clip0_1_1457)">
        <path d="M5.27273 10.8H15.8182V12.6H17.5758V14.4H19.3333V16.2H5.27273V27H0V0H5.27273V10.8ZM26.3636 27H21.0909V0H26.3636V27Z" fill="currentColor"/>
        <path d="M54.4849 1.8H56.2424V3.6H58V27H52.7273V5.4H36.9091V10.8H47.4545V12.6H49.2121V14.4H50.9697V16.2H36.9091V27H31.6364V0H54.4849V1.8Z" fill="currentColor"/>
        <path d="M114.242 7.2H116V25.2H114.242V27H96.6667V25.2H94.9091V7.2H96.6667V5.4H114.242V7.2ZM100.182 21.6H110.727V10.8H100.182V21.6Z" fill="currentColor"/>
        <path d="M86.1212 1.8H87.8788V3.6H89.6364V9H86.1212V7.2H84.3636V5.4H68.5455V21.6H77.3333V23.4H79.0909V25.2H80.8485V27H66.7879V25.2H65.0303V23.4H63.2727V0H86.1212V1.8ZM86.1212 14.4H87.8788V16.2H89.6364V27H84.3636V18H77.3333V16.2H75.5758V14.4H73.8182V12.6H86.1212V14.4Z" fill="currentColor"/>
      </g>
      <defs>
        <clipPath id="clip0_1_1457">
          <rect width="116" height="27" fill="white"/>
        </clipPath>
      </defs>
    </svg>
  </div>
  <div class="header-right">
    <button class="account-btn" id="accountBtn" onclick="openAccountModal()">登录</button>
  </div>
</header>

<div class="section">
  <button class="back-btn" onclick="goBack()">← 返回首页</button>
  <h2>设置</h2>

  <?php foreach ($settings as $item): ?>
    <?php if ($item->type === SettingType::Toggle): ?>
      <div class="setting-item">
        <div>
          <div class="setting-label"><?= e($item->label) ?></div>
          <div class="setting-desc"><?= e($item->description) ?></div>
        </div>
        <div class="toggle-switch" id="<?= e($item->jsSwitchId) ?>" onclick="<?= e($item->jsAction) ?>">
          <div class="toggle-slider"></div>
        </div>
      </div>
    <?php else: ?>
      <div class="setting-item">
        <div>
          <div class="setting-label"><?= e($item->label) ?></div>
          <div class="setting-desc"><?= e($item->description) ?></div>
        </div>
        <div style="color: <?= e($config->primaryColor) ?>; font-weight: bold;"><?= e($item->value) ?></div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<script>
var currentUser = null;

function loadUser() {
  var userStr = localStorage.getItem('hago_user');
  if (userStr) {
    currentUser = JSON.parse(userStr);
    var btn = document.getElementById('accountBtn');
    btn.textContent = currentUser.username;
    btn.classList.add('logout');
    btn.onclick = function() { logout(); };
  }
}

function logout() {
  localStorage.removeItem('hago_user');
  currentUser = null;
  var btn = document.getElementById('accountBtn');
  btn.textContent = '登录';
  btn.classList.remove('logout');
  btn.onclick = function() { openAccountModal(); };
}

function openAccountModal() {
  window.open('<?= e($config->homeUrl) ?>', '_self');
}

function toggleSidebar() {
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  
  if (sidebar.classList.contains('open')) {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    overlay.classList.add('closing');
    setTimeout(function() { overlay.classList.remove('closing'); }, 300);
  } else {
    sidebar.classList.add('open');
    overlay.classList.remove('closing');
    overlay.classList.add('active');
  }
}

function goBack() {
  window.location.href = '<?= e($config->homeUrl) ?>';
}

function loadDarkMode() {
  var isDark = localStorage.getItem('hago_dark_mode') === 'true';
  var body = document.body;
  var switchEl = document.getElementById('darkModeSwitch');
  
  if (isDark) {
    body.classList.add('dark-mode');
    switchEl.classList.add('on');
  }
}

function toggleDarkMode() {
  var body = document.body;
  var switchEl = document.getElementById('darkModeSwitch');
  var isDark = body.classList.contains('dark-mode');
  
  if (isDark) {
    body.classList.remove('dark-mode');
    switchEl.classList.remove('on');
    localStorage.setItem('hago_dark_mode', 'false');
  } else {
    body.classList.add('dark-mode');
    switchEl.classList.add('on');
    localStorage.setItem('hago_dark_mode', 'true');
  }
}

function loadAdvancedMaterials() {
  var isEnabled = localStorage.getItem('hago_advanced_materials') === 'true';
  var body = document.body;
  var switchEl = document.getElementById('advancedMaterialsSwitch');
  
  if (isEnabled) {
    body.classList.add('advanced-materials');
    switchEl.classList.add('on');
  }
}

function toggleAdvancedMaterials() {
  var body = document.body;
  var switchEl = document.getElementById('advancedMaterialsSwitch');
  var isEnabled = body.classList.contains('advanced-materials');
  
  if (isEnabled) {
    body.classList.remove('advanced-materials');
    switchEl.classList.remove('on');
    localStorage.setItem('hago_advanced_materials', 'false');
  } else {
    body.classList.add('advanced-materials');
    switchEl.classList.add('on');
    localStorage.setItem('hago_advanced_materials', 'true');
  }
}

loadUser();
loadDarkMode();
loadAdvancedMaterials();
</script>
</body>
</html>
