<?php
/**
 * Dra. Samla Cristie - Database Installer
 * Run this file ONCE to create all tables and seed initial data.
 * Access via browser: http://localhost/samycastro/install.php
 */

// Allow overriding DB credentials via form POST
$db_host = $_POST['db_host'] ?? 'localhost';
$db_name = $_POST['db_name'] ?? 'samlavet';
$db_user = $_POST['db_user'] ?? 'root';
$db_pass = $_POST['db_pass'] ?? '';
$admin_user = $_POST['admin_user'] ?? 'admin';
$admin_pass = $_POST['admin_pass'] ?? 'admin123';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connect without database first to create it
        $pdo = new PDO("mysql:host={$db_host};charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$db_name}`");

        // ---- Create Tables ----

        // Admin users
        $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Site settings (key-value)
        $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT,
            `setting_group` VARCHAR(50) DEFAULT 'geral',
            `setting_label` VARCHAR(200),
            `setting_type` VARCHAR(20) DEFAULT 'text',
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Services
        $pdo->exec("CREATE TABLE IF NOT EXISTS `services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `icon` VARCHAR(50) DEFAULT 'fas fa-paw',
            `image` VARCHAR(255),
            `whatsapp_text` VARCHAR(500),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Team members
        $pdo->exec("CREATE TABLE IF NOT EXISTS `team` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `role` VARCHAR(200),
            `description` TEXT,
            `image` VARCHAR(255),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Testimonials
        $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `initials` VARCHAR(5),
            `color` VARCHAR(20) DEFAULT '#4285f4',
            `rating` DECIMAL(2,1) DEFAULT 5.0,
            `text` TEXT NOT NULL,
            `date_label` VARCHAR(50),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Blog articles
        $pdo->exec("CREATE TABLE IF NOT EXISTS `articles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(300) NOT NULL,
            `slug` VARCHAR(300) NOT NULL UNIQUE,
            `excerpt` TEXT,
            `content` LONGTEXT,
            `image` VARCHAR(255),
            `category` VARCHAR(100),
            `author` VARCHAR(200) DEFAULT 'Dra. Samla Cristie',
            `read_time` VARCHAR(20) DEFAULT '5 min',
            `featured` TINYINT(1) DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `views` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // FAQ items
        $pdo->exec("CREATE TABLE IF NOT EXISTS `faq` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category` VARCHAR(100) NOT NULL,
            `category_icon` VARCHAR(50) DEFAULT 'fas fa-paw',
            `question` VARCHAR(500) NOT NULL,
            `answer` TEXT NOT NULL,
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Specialties
        $pdo->exec("CREATE TABLE IF NOT EXISTS `specialties` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `icon` VARCHAR(50) DEFAULT 'fas fa-star',
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Clients (partner clinics)
        $pdo->exec("CREATE TABLE IF NOT EXISTS `clients` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `type` VARCHAR(100),
            `description` TEXT,
            `logo_icon` VARCHAR(50) DEFAULT 'fas fa-hospital',
            `logo_color` VARCHAR(20) DEFAULT '#2d5016',
            `location` VARCHAR(200),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // ---- Insert Admin User ----
        $hashedPass = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)");
        $stmt->execute([$admin_user, $hashedPass, 'Administrador']);

        // ---- Insert Settings ----
        $settings = [
            // Hero
            ['hero_title', 'Dra. Samla Cristie', 'hero', 'Título Principal', 'text', 1],
            ['hero_subtitle', 'CRMV-GO 14064-VP', 'hero', 'Subtítulo (Registro)', 'text', 2],
            ['hero_tag', 'Medicina Veterinária com Amor e Dedicação', 'hero', 'Frase de Destaque', 'text', 3],
            ['hero_description', 'Cuidando dos seus animais com carinho, competência e paixão pelo campo. Atendimento domiciliar e rural para cães, gatos, equinos e animais silvestres.', 'hero', 'Descrição', 'textarea', 4],
            ['hero_image', '4.jpeg', 'hero', 'Imagem de Fundo', 'image', 5],

            // About
            ['about_title', 'Uma paixão que começou no campo', 'sobre', 'Título Sobre', 'text', 1],
            ['about_text1', 'Sou a <strong>Dra. Samla Cristie</strong>, médica veterinária registrada sob o CRMV-GO 14064-VP, apaixonada por animais desde a infância. Cresci em meio à natureza, rodeada de cavalos, cães e a fauna silvestre do cerrado goiano, e essa vivência moldou minha vocação.', 'sobre', 'Parágrafo 1', 'textarea', 2],
            ['about_text2', 'Formada em Medicina Veterinária, me especializei no atendimento ambulatorial e domiciliar, levando cuidado profissional diretamente onde o animal vive. Acredito que o ambiente familiar reduz o estresse do paciente e proporciona uma avaliação mais completa do seu dia a dia.', 'sobre', 'Parágrafo 2', 'textarea', 3],
            ['about_text3', 'Minha abordagem combina a <strong>medicina veterinária moderna</strong> com o <strong>respeito à natureza e ao bem-estar animal</strong>. Cada animal é único, e meu compromisso é oferecer um atendimento humanizado, acessível e de excelência.', 'sobre', 'Parágrafo 3', 'textarea', 4],
            ['about_image1', '3.jpeg', 'sobre', 'Imagem Principal', 'image', 5],
            ['about_image2', '7.jpeg', 'sobre', 'Imagem Secundária', 'image', 6],
            ['about_stat1_number', '500+', 'sobre', 'Estatística 1 - Número', 'text', 7],
            ['about_stat1_label', 'Atendimentos', 'sobre', 'Estatística 1 - Label', 'text', 8],
            ['about_stat2_number', '4', 'sobre', 'Estatística 2 - Número', 'text', 9],
            ['about_stat2_label', 'Especialidades', 'sobre', 'Estatística 2 - Label', 'text', 10],
            ['about_stat3_number', '100%', 'sobre', 'Estatística 3 - Número', 'text', 11],
            ['about_stat3_label', 'Dedicação', 'sobre', 'Estatística 3 - Label', 'text', 12],

            // CTA
            ['cta_title', 'Agende uma Consulta', 'cta', 'Título CTA', 'text', 1],
            ['cta_text', 'Nossa equipe está a postos para cuidar do seu animal com todo carinho e profissionalismo. Não espere — prevenir é sempre o melhor remédio!', 'cta', 'Texto CTA', 'textarea', 2],
            ['cta_image', '6.jpeg', 'cta', 'Imagem Fundo CTA', 'image', 3],

            // Team intro
            ['team_intro1', 'Nossa equipe é composta por profissionais com formação sólida e experiência prática nas mais diversas áreas da medicina veterinária. Atuamos com excelência em <strong>cirurgia geral</strong>, <strong>cirurgia ortopédica</strong>, <strong>nutrição animal</strong>, <strong>clínica de pequenos e grandes animais</strong>, <strong>medicina preventiva</strong> e <strong>cuidados intensivos</strong>.', 'equipe', 'Texto Intro Equipe 1', 'textarea', 1],
            ['team_intro2', 'Investimos constantemente em <strong>educação continuada</strong>, participando de congressos, simpósios e cursos de atualização para trazer sempre as melhores práticas e técnicas inovadoras aos nossos pacientes. Nosso diferencial é a combinação de conhecimento técnico com uma abordagem humanizada, tratando cada animal como se fosse nosso.', 'equipe', 'Texto Intro Equipe 2', 'textarea', 2],

            // Contact / Footer
            ['whatsapp_number', '5562994793553', 'contato', 'WhatsApp (só números)', 'text', 1],
            ['whatsapp_display', '(62) 99479-3553', 'contato', 'WhatsApp (exibição)', 'text', 2],
            ['contact_location', 'Goiânia e região - GO', 'contato', 'Localização', 'text', 3],
            ['contact_location_detail', 'Atendimento domiciliar e rural', 'contato', 'Detalhe Localização', 'text', 4],
            ['contact_email', 'contato@drasamlacristie.com.br', 'contato', 'E-mail', 'text', 5],
            ['hours_weekday', 'Seg a Sex: 08:00 às 18:00', 'contato', 'Horário Semana', 'text', 6],
            ['hours_saturday', 'Sáb: 08:00 às 12:00', 'contato', 'Horário Sábado', 'text', 7],
            ['footer_text', 'A melhor assistência veterinária para seus animais, unindo amor, competência e dedicação. Atendimento domiciliar e rural com toda a estrutura que seu animal merece.', 'contato', 'Texto Rodapé', 'textarea', 8],

            // Blog CTA
            ['blog_cta_title', '🐾 Agende uma Consulta — Nossa Equipe Está a Postos!', 'blog', 'Título CTA Blog', 'text', 1],
            ['blog_cta_text', 'Prevenir é o melhor caminho para uma vida longa e saudável para o seu animal. Não deixe para depois! Nossa equipe está pronta para oferecer o melhor atendimento veterinário, com amor e dedicação, diretamente no conforto da sua casa ou propriedade rural.', 'blog', 'Texto CTA Blog', 'textarea', 2],
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, setting_label, setting_type, sort_order) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($settings as $s) {
            $stmt->execute($s);
        }

        // ---- Insert Services ----
        $services = [
            ['Consulta para Cães', 'A consulta veterinária para cães é realizada no conforto do lar do animal, onde o pet se sente mais seguro e tranquilo. Realizamos avaliação clínica completa: auscultação cardiopulmonar, exame físico detalhado, verificação de mucosas, hidratação, palpação abdominal e avaliação comportamental. Também orientamos sobre vacinação, vermifugação, nutrição adequada e prevenção de doenças. O atendimento domiciliar permite observar o ambiente onde o animal vive, identificando possíveis riscos à saúde.', 'fas fa-dog', '18.jpeg', 'gostaria de agendar consulta para meu cão', 1],
            ['Consulta para Gatos', 'Gatos são naturalmente estressados em ambientes desconhecidos, por isso o atendimento domiciliar é ideal para felinos. A consulta inclui exame clínico completo, avaliação de pelagem, condição corporal, saúde bucal e comportamento. Verificamos calendário vacinal, orientamos sobre castração, desparasitação e enriquecimento ambiental. Oferecemos também orientações sobre nutrição felina, prevenção de doenças urinárias (muito comuns em gatos) e cuidados específicos para cada fase da vida do seu gato.', 'fas fa-cat', '10.jpeg', 'gostaria de agendar consulta para meu gato', 2],
            ['Consulta para Equinos', 'O atendimento equino é realizado diretamente na propriedade rural, haras ou cocheira onde o animal se encontra. A consulta abrange exame clínico geral, avaliação de cascos, dentição, sistema locomotor e respiratório. Realizamos vermifugação estratégica, vacinação, exames reprodutivos e orientações nutricionais específicas para cavalos de trabalho, esporte ou lazer. Também oferecemos suporte em emergências como cólicas equinas, ferimentos e avaliação pré-compra.', 'fas fa-horse', '5.jpeg', 'gostaria de agendar consulta para meu equino', 3],
            ['Animais Silvestres e Rurais', 'Atendemos animais silvestres legalizados e animais de produção rural. A consulta é feita no ambiente do animal, respeitando suas particularidades e necessidades específicas. Para silvestres, avaliamos condição geral, nutrição, enriquecimento ambiental e possíveis zoonoses. Para bovinos e outros animais de produção, realizamos exames clínicos, vacinação, manejo reprodutivo e sanitário. Cada espécie possui protocolos próprios de manuseio e contenção, garantindo segurança para o animal e a equipe.', 'fas fa-dove', '12.jpeg', 'gostaria de agendar consulta para animal silvestre', 4],
            ['Consultas Virtuais', 'Oferecemos teleconsultas veterinárias para orientação nutricional, acompanhamento pós-operatório, avaliação comportamental, segunda opinião clínica e dúvidas gerais sobre a saúde do seu animal. A consulta virtual é ideal para tutores que precisam de orientação profissional sem sair de casa. Atendemos por videochamada com horário agendado, proporcionando praticidade sem comprometer a qualidade do atendimento. Orientamos sobre dieta adequada, suplementação, manejo comportamental, cuidados com filhotes e acompanhamento de tratamentos em andamento.', 'fas fa-video', '9.jpeg', 'gostaria de agendar uma consulta virtual', 5],
            ['Parceria Técnica entre Clínicas', 'Oferecemos serviços veterinários terceirizados para clínicas e pet shops que precisam de suporte profissional especializado. Nossa parceria inclui: cirurgias gerais e ortopédicas sob demanda, atendimento ambulatorial em horários complementares, cobertura de emergências e plantões, consultoria em nutrição animal e protocolos clínicos, e apoio diagnóstico em casos complexos. Benefícios: redução de custos operacionais, acesso a profissionais capacitados sem vínculo fixo, flexibilidade de horários, qualidade garantida com registro CRMV, e ampliação do portfólio de serviços da sua clínica sem investimento em contratação.', 'fas fa-handshake', '8.jpeg', 'gostaria de saber sobre parceria técnica para minha clínica', 6],
        ];

        $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, image, whatsapp_text, sort_order) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title)");
        foreach ($services as $s) {
            $stmt->execute($s);
        }

        // ---- Insert Team ----
        $team = [
            ['Dra. Samla Cristie', 'Médica Veterinária — CRMV-GO 14064-VP', 'Especialista em clínica de pequenos e grandes animais, cirurgia geral e atendimento ambulatorial domiciliar.', '2.jpeg', 1],
            ['Capacitação Contínua', 'Palestras e Congressos', 'Participação ativa em eventos científicos, ministrando palestras sobre tratamentos inovadores e saúde animal.', '9.jpeg', 2],
            ['Vacinação e Medicação', 'Protocolos Atualizados', 'Seguimos protocolos rigorosos de vacinação e medicação, utilizando produtos de qualidade e técnicas seguras de aplicação.', '16.jpeg', 3],
            ['Atendimento Equino', 'Manejo e Cuidados', 'Atendimento especializado para equinos com foco em ortopedia, nutrição e manejo reprodutivo diretamente na propriedade.', '15.jpeg', 4],
        ];

        $stmt = $pdo->prepare("INSERT INTO team (name, role, description, image, sort_order) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        foreach ($team as $t) {
            $stmt->execute($t);
        }

        // ---- Insert Testimonials ----
        $testimonials = [
            ['Maria Fernanda S.', 'MF', '#4285f4', 5.0, '"A Dra. Samla é simplesmente maravilhosa! Atendeu meus dois cachorros em casa, com muita paciência e carinho. Explicou tudo direitinho sobre a vacinação e os cuidados. Super recomendo!"', 'há 2 semanas', 1],
            ['João Carlos M.', 'JC', '#ea4335', 5.0, '"Chamei a Dra. Samla para atender meus cavalos na fazenda. Profissional incrível, pontual e muito competente. Fez exames completos e orientou sobre o manejo nutricional. Nota 10!"', 'há 1 mês', 2],
            ['Ana Clara R.', 'AC', '#fbbc05', 5.0, '"Minha gatinha Mimi ficou doente e a Dra. Samla veio atender em casa no mesmo dia. Muito atenciosa, tratou com muito carinho e a Mimi melhorou super rápido. Deus abençoe!"', 'há 1 mês', 3],
            ['Pedro Henrique L.', 'PH', '#34a853', 4.5, '"Excelente profissional! Atendeu meu bezerro que estava com problemas respiratórios. Muito dedicada e conhece muito sobre animais de produção. O atendimento rural dela é diferenciado."', 'há 2 meses', 4],
            ['Luciana B.', 'LB', '#673ab7', 5.0, '"Melhor veterinária da região! A Dra. Samla cuidou da cirurgia do meu dog e foi impecável. O pós-operatório foi excelente e ela acompanhou cada detalhe. Muito grata!"', 'há 3 meses', 5],
            ['Renato F.', 'RF', '#ff5722', 5.0, '"A Dra. Samla atendeu meus cavalos de vaquejada e mostrou um conhecimento excepcional em equinos. Muito profissional, atenciosa e apaixonada pelo que faz. Recomendo demais!"', 'há 3 meses', 6],
        ];

        $stmt = $pdo->prepare("INSERT INTO testimonials (name, initials, color, rating, text, date_label, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        foreach ($testimonials as $t) {
            $stmt->execute($t);
        }

        // ---- Insert Articles ----
        $articles = [
            ['Cuidados Essenciais com Cães: Guia Completo', 'cuidados-essenciais-caes-guia-completo',
             'Seu cão precisa de atenção especial em todas as fases da vida. Saiba quais são os sinais de alerta, a importância do check-up anual, como manter a vacinação em dia e dicas de nutrição adequada para cada porte.',
             '<h2>A importância do check-up anual</h2>
<p>Assim como os humanos, os cães precisam de consultas regulares com o veterinário. O check-up anual é fundamental para detectar precocemente doenças que podem comprometer a qualidade de vida do seu pet.</p>
<p>Durante a consulta de rotina, o veterinário realiza:</p>
<ul>
<li><strong>Exame físico completo</strong>: avaliação de pele, pelagem, olhos, ouvidos, boca, coração e pulmões</li>
<li><strong>Verificação do peso</strong>: obesidade é um problema crescente em cães domésticos</li>
<li><strong>Avaliação do calendário vacinal</strong>: verificar se as vacinas estão em dia</li>
<li><strong>Exames laboratoriais</strong>: hemograma, bioquímico e urinálise quando necessário</li>
</ul>

<h2>Vacinação: o calendário completo</h2>
<p>A vacinação é a principal forma de prevenir doenças graves em cães. O protocolo vacinal inicia entre 6 a 8 semanas de vida:</p>
<ul>
<li><strong>V8 ou V10 (Polivalente)</strong>: protege contra cinomose, parvovirose, hepatite infecciosa, leptospirose, entre outras. São necessárias 3 doses, com intervalo de 21 a 30 dias.</li>
<li><strong>Antirrábica</strong>: dose única a partir de 4 meses de idade, com reforço anual.</li>
<li><strong>Gripe Canina</strong>: recomendada para cães que frequentam pet shops, hotéis e creches caninas.</li>
<li><strong>Giardíase</strong>: indicada especialmente para filhotes e cães de áreas endêmicas.</li>
</ul>

<h2>Nutrição adequada para cada fase</h2>
<p>A alimentação do cão deve ser adaptada à sua idade, porte e nível de atividade:</p>
<ul>
<li><strong>Filhotes</strong>: ração super premium para filhotes, rica em proteínas e cálcio para o crescimento</li>
<li><strong>Adultos</strong>: ração de manutenção adequada ao porte (mini, médio, grande ou gigante)</li>
<li><strong>Idosos</strong>: ração sênior com glucosamina e condroitina para as articulações</li>
</ul>
<p>Evite oferecer alimentos proibidos como chocolate, cebola, alho, uvas, passas e alimentos temperados.</p>

<h2>Vermifugação e controle de parasitas</h2>
<p>A vermifugação deve ser realizada a cada 3 meses em cães adultos. Filhotes precisam de vermifugação mais frequente, a cada 15 dias até os 3 meses de vida.</p>
<p>O controle de pulgas e carrapatos é igualmente importante, pois esses parasitas podem transmitir doenças graves como a Erliquiose Canina e a Babesiose.</p>

<h2>Sinais de alerta: quando correr ao veterinário</h2>
<p>Fique atento a estes sinais que indicam que seu cão precisa de atendimento imediato:</p>
<ul>
<li>Vômitos ou diarreia persistentes (mais de 24 horas)</li>
<li>Recusa alimentar por mais de 1 dia</li>
<li>Dificuldade para respirar ou tosse intensa</li>
<li>Sangramento de qualquer origem</li>
<li>Abdômen inchado e dolorido</li>
<li>Incapacidade de urinar</li>
<li>Convulsões</li>
<li>Apatia extrema ou desorientação</li>
</ul>',
             '11.jpeg', 'Cães', 'Dra. Samla Cristie', '5 min', 1, 1],

            ['Gatos: Sinais de Doença que Você Precisa Conhecer', 'gatos-sinais-doenca-conhecer',
             'Gatos são mestres em esconder dor e desconforto. Aprenda a identificar os sinais sutis de que algo não está bem.',
             '<h2>Por que gatos escondem a dor?</h2>
<p>Na natureza, mostrar fraqueza pode significar ser alvo de predadores. Por isso, os gatos desenvolveram um instinto poderoso de esconder sinais de dor e desconforto. Isso torna o papel do tutor fundamental na detecção precoce de problemas de saúde.</p>

<h2>Sinais sutis que indicam problemas</h2>
<p>Fique atento às seguintes mudanças no comportamento do seu gato:</p>
<ul>
<li><strong>Mudança de apetite</strong>: comer menos ou mais que o habitual</li>
<li><strong>Alteração na sede</strong>: beber muita ou pouca água</li>
<li><strong>Mudança no uso da caixa de areia</strong>: urinar fora, dificuldade para urinar, sangue na urina</li>
<li><strong>Isolamento</strong>: esconder-se em locais incomuns</li>
<li><strong>Agressividade</strong>: gato normalmente dócil que se torna agressivo</li>
<li><strong>Vocalização excessiva</strong>: miados constantes e diferentes do habitual</li>
<li><strong>Alteração na pelagem</strong>: pelo opaco, queda excessiva ou lambedura compulsiva</li>
</ul>

<h2>Doenças comuns em gatos</h2>
<h3>Doença do Trato Urinário Inferior Felino (DTUIF)</h3>
<p>Muito comum especialmente em machos, pode causar obstrução urinária — uma emergência veterinária. Sinais incluem idas frequentes à caixa de areia, miados de dor ao urinar e sangue na urina.</p>

<h3>Doença Renal Crônica</h3>
<p>Afeta principalmente gatos idosos. Sinais incluem aumento da sede, perda de peso e vômitos frequentes. O diagnóstico precoce permite um manejo adequado que prolonga a vida do gato.</p>

<h3>Diabetes Mellitus</h3>
<p>Gatos obesos têm maior predisposição. Sinais: sede excessiva, aumento do apetite com perda de peso e urina em grande quantidade.</p>

<h2>Prevenção é o melhor remédio</h2>
<p>Leve seu gato ao veterinário pelo menos uma vez ao ano. Gatos idosos (acima de 7 anos) devem fazer check-up a cada 6 meses. Mantenha a vacinação em dia, ofereça alimentação de qualidade e proporcione enriquecimento ambiental com arranhadores, prateleiras e brinquedos.</p>',
             '17.jpeg', 'Gatos', 'Dra. Samla Cristie', '4 min', 0, 1],

            ['Calendário de Vacinação para Equinos', 'calendario-vacinacao-equinos',
             'Manter o calendário vacinal dos cavalos em dia é essencial para prevenir doenças como influenza equina, encefalomielite, tétano e raiva.',
             '<h2>A importância da vacinação em equinos</h2>
<p>A vacinação é a principal ferramenta de prevenção contra doenças infecciosas em cavalos. Um programa vacinal bem elaborado protege não apenas o animal, mas todo o plantel e a saúde pública.</p>

<h2>Vacinas essenciais para equinos</h2>

<h3>Raiva</h3>
<p><strong>Obrigatória por lei.</strong> A raiva é uma doença fatal que pode ser transmitida para humanos. A vacinação é feita anualmente, com a primeira dose a partir de 3 meses de idade.</p>

<h3>Influenza Equina (Gripe)</h3>
<p>Doença respiratória altamente contagiosa. O protocolo inclui duas doses iniciais com intervalo de 4-6 semanas, seguidas de reforço semestral para cavalos de esporte e anual para os demais.</p>

<h3>Tétano</h3>
<p>Os cavalos são muito sensíveis à toxina tetânica. A vacinação é essencial, especialmente considerando que os equinos estão constantemente expostos a ferimentos. Protocolo: duas doses iniciais com 4-6 semanas de intervalo, reforço anual.</p>

<h3>Encefalomielite (Leste e Oeste)</h3>
<p>Doenças neurológicas transmitidas por mosquitos. A vacinação é feita anualmente, preferencialmente antes do período chuvoso.</p>

<h3>Herpesvírus Equino (Rinopneumonite)</h3>
<p>Causa abortos em éguas, problemas respiratórios e, em casos graves, síndrome neurológica. Éguas gestantes devem ser vacinadas nos 3°, 5°, 7° e 9° meses de gestação.</p>

<h2>Calendário resumido</h2>
<ul>
<li><strong>Janeiro/Fevereiro</strong>: Encefalomielite + Influenza</li>
<li><strong>Março/Abril</strong>: Raiva + Tétano</li>
<li><strong>Julho/Agosto</strong>: Reforço Influenza (cavalos de esporte)</li>
<li><strong>Outubro/Novembro</strong>: Reforço Encefalomielite</li>
</ul>

<h2>Vermifugação estratégica</h2>
<p>A vermifugação deve ser feita com base em exames de OPG (contagem de ovos por grama de fezes). Em geral, o protocolo inclui vermifugação a cada 3-4 meses, utilizando diferentes princípios ativos para evitar resistência parasitária.</p>

<h2>Cuidados com o casco</h2>
<p>O casqueamento ou ferrageamento deve ser feito a cada 30-45 dias. Cascos mal cuidados podem causar problemas locomotores graves, como laminite e naviculite.</p>',
             '8.jpeg', 'Equinos', 'Dra. Samla Cristie', '6 min', 0, 1],

            ['Quando Levar seu Animal ao Veterinário: Guia de Emergência', 'guia-emergencia-veterinaria',
             'Saiba identificar situações de urgência e emergência veterinária e aprenda os primeiros socorros para animais.',
             '<h2>Diferença entre urgência e emergência</h2>
<p>É importante saber diferenciar uma situação de urgência de uma emergência veterinária:</p>
<ul>
<li><strong>Urgência</strong>: situação que requer atendimento em poucas horas, mas sem risco imediato de vida. Ex: vômito isolado, ferimento superficial, claudicação leve.</li>
<li><strong>Emergência</strong>: risco iminente de vida que requer atendimento IMEDIATO. Ex: atropelamento, convulsões, dificuldade respiratória grave, envenenamento.</li>
</ul>

<h2>Situações que exigem atendimento imediato</h2>

<h3>🚨 Dificuldade respiratória</h3>
<p>Se seu animal está respirando com a boca aberta (especialmente gatos), fazendo ruídos ao respirar ou com as mucosas azuladas (cianose), procure ajuda IMEDIATA.</p>

<h3>🚨 Convulsões</h3>
<p>Mantenha a calma, afaste objetos que possam machucá-lo, não tente segurar a língua do animal. Registre a duração da convulsão e procure o veterinário imediatamente.</p>

<h3>🚨 Envenenamento</h3>
<p><strong>NÃO INDUZA O VÔMITO</strong> sem orientação veterinária. Anote o que o animal ingeriu, a quantidade e o horário. Substâncias perigosas comuns:</p>
<ul>
<li>Raticidas (chumbinho)</li>
<li>Medicamentos humanos (dipirona, ibuprofeno, paracetamol)</li>
<li>Produtos de limpeza</li>
<li>Plantas tóxicas (lírio, comigo-ninguém-pode, espada-de-são-jorge)</li>
<li>Alimentos (chocolate, cebola, alho, uvas)</li>
</ul>

<h3>🚨 Trauma (atropelamento, queda)</h3>
<p>Imobilize o animal com cuidado, usando uma tábua ou cobertor firme como maca improvisada. Controle sangramentos com pressão direta usando pano limpo. Transporte com o mínimo de movimento possível.</p>

<h2>Kit de primeiros socorros para pets</h2>
<p>Tenha sempre em casa:</p>
<ul>
<li>Gaze estéril e ataduras</li>
<li>Soro fisiológico para limpeza de ferimentos</li>
<li>Termômetro digital (temperatura normal: cães 38-39°C, gatos 38-39.5°C)</li>
<li>Luvas descartáveis</li>
<li>Telefone do veterinário sempre acessível</li>
</ul>

<h2>Importante: não medique seu animal sem orientação</h2>
<p>Muitos medicamentos humanos são TÓXICOS para animais. Dipirona e paracetamol, por exemplo, podem ser fatais para gatos. Sempre consulte um veterinário antes de administrar qualquer medicamento.</p>',
             '1.jpeg', 'Dicas Gerais', 'Dra. Samla Cristie', '7 min', 0, 1],
        ];

        $stmt = $pdo->prepare("INSERT INTO articles (title, slug, excerpt, content, image, category, author, read_time, featured, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title)");
        foreach ($articles as $a) {
            $stmt->execute($a);
        }

        // ---- Insert FAQ ----
        $faqs = [
            ['Cães e Gatos', 'fas fa-paw', 'Qual a frequência ideal de consultas para cães e gatos?', 'Para animais saudáveis, recomenda-se consultas anuais de check-up. Filhotes e animais idosos devem ser acompanhados com mais frequência — a cada 3 a 6 meses. Animais com doenças crônicas precisam de acompanhamento mais próximo, conforme orientação do veterinário.', 1],
            ['Cães e Gatos', 'fas fa-paw', 'Quando devo iniciar a vacinação do meu filhote?', 'A vacinação de filhotes de cães geralmente inicia entre 6 a 8 semanas de vida, com a vacina V8 ou V10 (polivalente), seguida de reforços a cada 21-30 dias até completar 3 doses. Para gatos, inicia-se com a tríplice felina (V3 ou V4) na mesma idade. A vacina antirrábica é aplicada a partir de 4 meses de idade.', 2],
            ['Cães e Gatos', 'fas fa-paw', 'Qual a importância da castração?', 'A castração previne doenças graves como tumores mamários, piometra (infecção uterina) e problemas prostáticos. Além disso, reduz comportamentos indesejados como marcação territorial, agressividade e fugas. O procedimento é seguro e recomendado a partir de 6 meses de idade.', 3],
            ['Equinos', 'fas fa-horse', 'Com que frequência devo vermifugar meu cavalo?', 'Recomenda-se a vermifugação estratégica de equinos, idealmente com exame de OPG (contagem de ovos por grama de fezes) para guiar o protocolo. Em geral, cavalos adultos são vermifugados a cada 3-4 meses, mas animais jovens e éguas podem precisar de protocolos mais frequentes.', 4],
            ['Equinos', 'fas fa-horse', 'O que é cólica equina e como prevenir?', 'Cólica equina é toda dor abdominal no cavalo, podendo ter diversas causas: alimentação inadequada, verminoses, torção intestinal, entre outras. É uma emergência veterinária! Para prevenir: mantenha alimentação regular e de qualidade, água limpa sempre disponível, vermifugação em dia e evite mudanças bruscas na dieta.', 5],
            ['Equinos', 'fas fa-horse', 'Quais vacinas são obrigatórias para cavalos?', 'As vacinas essenciais para equinos incluem: raiva (obrigatória por lei), influenza equina, encefalomielite (Leste e Oeste), tétano e herpesvírus equino. O calendário vacinal deve ser adaptado à região e atividade do animal. Consulte sua veterinária para o protocolo ideal.', 6],
            ['Aves e Animais Silvestres', 'fas fa-dove', 'Posso ter um animal silvestre como pet?', 'Sim, desde que o animal seja adquirido em criadouros ou estabelecimentos comerciais autorizados pelo IBAMA, com nota fiscal e documentação legal. É crime criar animais silvestres sem autorização. Em caso de encontrar um animal silvestre ferido, entre em contato com o IBAMA ou a polícia ambiental.', 7],
            ['Aves e Animais Silvestres', 'fas fa-dove', 'Quais cuidados devo ter com aves domésticas?', 'Aves precisam de alimentação balanceada (ração específica + frutas e legumes), gaiola ou viveiro limpo e espaçoso, água fresca diária, exposição controlada ao sol e enriquecimento ambiental com brinquedos e poleiros variados. Consultas veterinárias periódicas são essenciais para detectar doenças respiratórias e deficiências nutricionais.', 8],
            ['Emergência e Urgência', 'fas fa-ambulance', 'Meu animal ingeriu algo tóxico, o que fazer?', '<strong>NÃO induza o vômito sem orientação veterinária!</strong> Alguns produtos podem causar mais danos ao voltar. Anote o que o animal ingeriu, a quantidade aproximada e o horário. Entre em contato imediatamente com um veterinário. Substâncias comuns perigosas incluem: chocolate, cebola, uvas, medicamentos humanos, produtos de limpeza e raticidas.', 9],
            ['Emergência e Urgência', 'fas fa-ambulance', 'Como agir em caso de hemorragia no animal?', 'Em caso de sangramento intenso, aplique pressão direta sobre o ferimento com um pano limpo. Mantenha a pressão por pelo menos 5 minutos sem remover. NÃO use torniquete caseiro, pois pode piorar a situação. Mantenha o animal calmo e transporte-o imediatamente ao veterinário mais próximo.', 10],
            ['Emergência e Urgência', 'fas fa-ambulance', 'Quais sinais indicam que meu animal precisa de atendimento urgente?', 'Procure atendimento imediato se observar: dificuldade para respirar, convulsões, vômitos ou diarreia persistentes, sangramento que não para, abdômen inchado e dolorido, incapacidade de urinar, trauma grave (atropelamento, queda), ingestão de corpo estranho ou substâncias tóxicas. Em qualquer dúvida, ligue para o veterinário!', 11],
        ];

        $stmt = $pdo->prepare("INSERT INTO faq (category, category_icon, question, answer, sort_order) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question = VALUES(question)");
        foreach ($faqs as $f) {
            $stmt->execute($f);
        }

        // ---- Insert Specialties ----
        $specialties = [
            ['Cirurgia Geral', 'fas fa-cut', 1],
            ['Cirurgia Ortopédica', 'fas fa-bone', 2],
            ['Nutrição Animal', 'fas fa-apple-alt', 3],
            ['Clínica Geral', 'fas fa-heartbeat', 4],
            ['Vacinação', 'fas fa-syringe', 5],
            ['Neonatologia', 'fas fa-baby', 6],
            ['Dermatologia', 'fas fa-bug', 7],
            ['Odontologia Veterinária', 'fas fa-teeth', 8],
            ['Diagnóstico por Imagem', 'fas fa-x-ray', 9],
            ['Emergência e Urgência', 'fas fa-ambulance', 10],
            ['Reprodução Animal', 'fas fa-venus-mars', 11],
            ['Medicina Preventiva', 'fas fa-shield-alt', 12],
            ['Telemedicina Veterinária', 'fas fa-video', 13],
            ['Terceirização para Clínicas', 'fas fa-handshake', 14],
        ];

        $stmt = $pdo->prepare("INSERT INTO specialties (name, icon, sort_order) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        foreach ($specialties as $sp) {
            $stmt->execute($sp);
        }

        // ---- Insert Clients ----
        $clients = [
            ['Doma Pet', 'Pet Shop & Clínica', 'Pet shop parceiro com serviços veterinários terceirizados. Atendimento clínico, vacinação e cirurgias realizadas pela nossa equipe.', 'fas fa-store', '#e67e22', 'Goiânia - GO', 1],
            ['Vet Clín', 'Clínica Veterinária', 'Clínica veterinária de referência com parceria em cirurgias especializadas e cobertura de plantões noturnos e finais de semana.', 'fas fa-hospital', '#3498db', 'Aparecida de Goiânia - GO', 2],
            ['PetVida Centro Veterinário', 'Hospital Veterinário', 'Hospital veterinário com suporte em ortopedia e cirurgia geral, além de consultoria em nutrição animal para pacientes internados.', 'fas fa-clinic-medical', '#2ecc71', 'Goiânia - GO', 3],
            ['Agro Saúde Animal', 'Agropecuária', 'Agropecuária parceira para atendimento de animais de grande porte, equinos e bovinos na região metropolitana de Goiânia.', 'fas fa-tractor', '#8B4513', 'Senador Canedo - GO', 4],
            ['Amigo Fiel Pet Center', 'Pet Shop', 'Pet center com parceria em atendimento clínico ambulatorial, vacinação em campanha e orientação nutricional para clientes.', 'fas fa-paw', '#9b59b6', 'Trindade - GO', 5],
            ['CliniVet Goiás', 'Clínica Veterinária', 'Clínica parceira com foco em dermatologia e diagnóstico por imagem, com suporte cirúrgico da nossa equipe quando necessário.', 'fas fa-stethoscope', '#e74c3c', 'Anápolis - GO', 6],
            ['Rancho Bom Animal', 'Fazenda & Haras', 'Fazenda e haras com contrato de atendimento veterinário regular para equinos de esporte e animais de produção.', 'fas fa-horse', '#c9a84c', 'Pirenópolis - GO', 7],
            ['PetLove Clínica', 'Clínica & Estética', 'Clínica veterinária com parceria em procedimentos cirúrgicos, emergências e acompanhamento nutricional pós-operatório.', 'fas fa-heart', '#e91e63', 'Goiânia - GO', 8],
        ];

        $stmt = $pdo->prepare("INSERT INTO clients (name, type, description, logo_icon, logo_color, location, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        foreach ($clients as $c) {
            $stmt->execute($c);
        }

        // Create uploads directory
        $uploadsDir = __DIR__ . '/uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        // Update config.php with the provided credentials
        $configContent = "<?php
/**
 * Dra. Samla Cristie - Database Configuration
 */

define('DB_HOST', " . var_export($db_host, true) . ");
define('DB_NAME', " . var_export($db_name, true) . ");
define('DB_USER', " . var_export($db_user, true) . ");
define('DB_PASS', " . var_export($db_pass, true) . ");
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Dra. Samla Cristie');
define('SITE_URL', '');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDB() {
    static \$pdo = null;
    if (\$pdo === null) {
        try {
            \$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException \$e) {
            die('Erro na conexão com o banco de dados: ' . \$e->getMessage());
        }
    }
    return \$pdo;
}

function isLoggedIn() {
    return isset(\$_SESSION['admin_logged_in']) && \$_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function e(\$str) {
    return htmlspecialchars(\$str ?? '', ENT_QUOTES, 'UTF-8');
}

function getSetting(\$key, \$default = '') {
    try {
        \$db = getDB();
        \$stmt = \$db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        \$stmt->execute([\$key]);
        \$row = \$stmt->fetch();
        return \$row ? \$row['setting_value'] : \$default;
    } catch (Exception \$e) {
        return \$default;
    }
}

function getAllSettings() {
    try {
        \$db = getDB();
        \$stmt = \$db->query('SELECT setting_key, setting_value FROM settings');
        \$settings = [];
        while (\$row = \$stmt->fetch()) {
            \$settings[\$row['setting_key']] = \$row['setting_value'];
        }
        return \$settings;
    } catch (Exception \$e) {
        return [];
    }
}
";
        file_put_contents(__DIR__ . '/config.php', $configContent);

        $success = true;

    } catch (PDOException $e) {
        $errors[] = 'Erro no banco de dados: ' . $e->getMessage();
    } catch (Exception $e) {
        $errors[] = 'Erro: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Dra. Samla Cristie</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f4e8; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .install-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.1); padding: 48px; max-width: 520px; width: 100%; }
        .install-header { text-align: center; margin-bottom: 32px; }
        .install-header h1 { font-size: 1.5rem; color: #2d5016; margin-bottom: 8px; }
        .install-header p { color: #666; font-size: 0.9rem; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #333; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input { width: 100%; padding: 12px 16px; border: 2px solid #e5ddd0; border-radius: 10px; font-size: 0.9rem; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #2d5016; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .section-title { font-size: 0.9rem; color: #2d5016; font-weight: 600; margin: 24px 0 12px; padding-bottom: 8px; border-bottom: 2px solid #e8f0e2; }
        .btn-install { width: 100%; padding: 16px; background: #2d5016; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.3s; margin-top: 24px; }
        .btn-install:hover { background: #3d6b22; }
        .alert { padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 0.88rem; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .alert-error { background: #fce4ec; color: #c62828; border: 1px solid #f8bbd0; }
        .alert a { color: inherit; font-weight: 600; }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="install-header">
            <h1>🐾 Instalação do Sistema</h1>
            <p>Configure o banco de dados e o acesso administrativo</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <strong>Instalação concluída com sucesso!</strong><br><br>
                Tabelas criadas e dados inseridos.<br>
                <strong>Usuário admin:</strong> <?= htmlspecialchars($admin_user) ?><br><br>
                <a href="admin/">→ Acessar Painel Administrativo</a><br>
                <a href="index.php">→ Ver o Site</a><br><br>
                ⚠️ <strong>Exclua ou renomeie este arquivo (install.php) após a instalação!</strong>
            </div>
        <?php endif; ?>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="section-title">🗄️ Banco de Dados MySQL</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Host</label>
                    <input type="text" name="db_host" value="<?= htmlspecialchars($db_host) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nome do Banco</label>
                    <input type="text" name="db_name" value="<?= htmlspecialchars($db_name) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Usuário MySQL</label>
                    <input type="text" name="db_user" value="<?= htmlspecialchars($db_user) ?>" required>
                </div>
                <div class="form-group">
                    <label>Senha MySQL</label>
                    <input type="password" name="db_pass" value="">
                </div>
            </div>

            <div class="section-title">🔐 Credenciais do Admin</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Usuário</label>
                    <input type="text" name="admin_user" value="<?= htmlspecialchars($admin_user) ?>" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="admin_pass" value="" placeholder="Mínimo 6 caracteres" required>
                </div>
            </div>

            <button type="submit" class="btn-install">🚀 Instalar Sistema</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
