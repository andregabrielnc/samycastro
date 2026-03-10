-- Dra. Samla Cristie - Database Initialization Script
-- This script is run automatically when the MySQL container starts

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Admin users
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site settings
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_group` VARCHAR(50) DEFAULT 'geral',
    `setting_label` VARCHAR(200),
    `setting_type` VARCHAR(20) DEFAULT 'text',
    `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services
CREATE TABLE IF NOT EXISTS `services` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team members
CREATE TABLE IF NOT EXISTS `team` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `role` VARCHAR(200),
    `description` TEXT,
    `image` VARCHAR(255),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Testimonials
CREATE TABLE IF NOT EXISTS `testimonials` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog articles
CREATE TABLE IF NOT EXISTS `articles` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FAQ items
CREATE TABLE IF NOT EXISTS `faq` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(100) NOT NULL,
    `category_icon` VARCHAR(50) DEFAULT 'fas fa-paw',
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Specialties
CREATE TABLE IF NOT EXISTS `specialties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `icon` VARCHAR(50) DEFAULT 'fas fa-star',
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clients
CREATE TABLE IF NOT EXISTS `clients` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SAMPLE DATA - Dados de Exemplo
-- ============================================

-- Configurações do Site (Settings)
INSERT IGNORE INTO `settings` (`setting_key`,`setting_value`,`setting_group`,`setting_label`,`setting_type`,`sort_order`) VALUES
-- Hero
('hero_title','Dra. Samla Cristie','hero','Título Principal','text',1),
('hero_subtitle','CRMV-GO 14064-VP','hero','Subtítulo (Registro)','text',2),
('hero_tag','Medicina Veterinária com Amor e Dedicação','hero','Frase de Destaque','text',3),
('hero_description','Cuidando dos seus animais com carinho, competência e paixão pelo campo. Atendimento domiciliar e rural para cães, gatos, equinos e animais silvestres.','hero','Descrição','textarea',4),
('hero_image','4.jpeg','hero','Imagem de Fundo','image',5),
-- Sobre
('about_title','Uma paixão que começou no campo','sobre','Título Sobre','text',1),
('about_text1','Sou a <strong>Dra. Samla Cristie</strong>, médica veterinária registrada sob o CRMV-GO 14064-VP, apaixonada por animais desde a infância. Cresci em meio à natureza, rodeada de cavalos, cães e a fauna silvestre do cerrado goiano, e essa vivência moldou minha vocação.','sobre','Parágrafo 1','textarea',2),
('about_text2','Formada em Medicina Veterinária, me especializei no atendimento ambulatorial e domiciliar, levando cuidado profissional diretamente onde o animal vive. Acredito que o ambiente familiar reduz o estresse do paciente e proporciona uma avaliação mais completa do seu dia a dia.','sobre','Parágrafo 2','textarea',3),
('about_text3','Minha abordagem combina a <strong>medicina veterinária moderna</strong> com o <strong>respeito à natureza e ao bem-estar animal</strong>. Cada animal é único, e meu compromisso é oferecer um atendimento humanizado, acessível e de excelência.','sobre','Parágrafo 3','textarea',4),
('about_image1','3.jpeg','sobre','Imagem Principal','image',5),
('about_image2','7.jpeg','sobre','Imagem Secundária','image',6),
('about_stat1_number','500+','sobre','Estatística 1 - Número','text',7),
('about_stat1_label','Atendimentos','sobre','Estatística 1 - Label','text',8),
('about_stat2_number','4','sobre','Estatística 2 - Número','text',9),
('about_stat2_label','Especialidades','sobre','Estatística 2 - Label','text',10),
('about_stat3_number','100%','sobre','Estatística 3 - Número','text',11),
('about_stat3_label','Dedicação','sobre','Estatística 3 - Label','text',12),
-- CTA
('cta_title','Agende uma Consulta','cta','Título CTA','text',1),
('cta_text','Nossa equipe está a postos para cuidar do seu animal com todo carinho e profissionalismo. Não espere — prevenir é sempre o melhor remédio!','cta','Texto CTA','textarea',2),
('cta_image','6.jpeg','cta','Imagem Fundo CTA','image',3),
-- Equipe
('team_intro1','Nossa equipe é composta por profissionais com formação sólida e experiência prática nas mais diversas áreas da medicina veterinária. Atuamos com excelência em <strong>cirurgia geral</strong>, <strong>cirurgia ortopédica</strong>, <strong>nutrição animal</strong>, <strong>clínica de pequenos e grandes animais</strong>, <strong>medicina preventiva</strong> e <strong>cuidados intensivos</strong>.','equipe','Texto Intro Equipe 1','textarea',1),
('team_intro2','Investimos constantemente em <strong>educação continuada</strong>, participando de congressos, simpósios e cursos de atualização para trazer sempre as melhores práticas e técnicas inovadoras aos nossos pacientes. Nosso diferencial é a combinação de conhecimento técnico com uma abordagem humanizada, tratando cada animal como se fosse nosso.','equipe','Texto Intro Equipe 2','textarea',2),
-- Contato / Rodapé
('whatsapp_number','5562994793553','contato','WhatsApp (só números)','text',1),
('whatsapp_display','(62) 99479-3553','contato','WhatsApp (exibição)','text',2),
('contact_location','Goiânia e região - GO','contato','Localização','text',3),
('contact_location_detail','Atendimento domiciliar e rural','contato','Detalhe Localização','text',4),
('contact_email','contato@drasamlacristie.com.br','contato','E-mail','text',5),
('hours_weekday','Seg a Sex: 08:00 às 18:00','contato','Horário Semana','text',6),
('hours_saturday','Sáb: 08:00 às 12:00','contato','Horário Sábado','text',7),
('footer_text','A melhor assistência veterinária para seus animais, unindo amor, competência e dedicação. Atendimento domiciliar e rural com toda a estrutura que seu animal merece.','contato','Texto Rodapé','textarea',8),
-- Blog
('blog_cta_title','🐾 Agende uma Consulta — Nossa Equipe Está a Postos!','blog','Título CTA Blog','text',1),
('blog_cta_text','Prevenir é o melhor caminho para uma vida longa e saudável para o seu animal. Não deixe para depois! Nossa equipe está pronta para oferecer o melhor atendimento veterinário, com amor e dedicação, diretamente no conforto da sua casa ou propriedade rural.','blog','Texto CTA Blog','textarea',2);

-- Serviços
INSERT IGNORE INTO `services` (`title`,`description`,`icon`,`image`,`whatsapp_text`,`sort_order`,`active`) VALUES
('Consulta para Cães','A consulta veterinária para cães é realizada no conforto do lar do animal, onde o pet se sente mais seguro e tranquilo. Realizamos avaliação clínica completa: auscultação cardiopulmonar, exame físico detalhado, verificação de mucosas, hidratação, palpação abdominal e avaliação comportamental. Também orientamos sobre vacinação, vermifugação, nutrição adequada e prevenção de doenças. O atendimento domiciliar permite observar o ambiente onde o animal vive, identificando possíveis riscos à saúde.','fas fa-dog','18.jpeg','gostaria de agendar consulta para meu cão',1,1),
('Consulta para Gatos','Gatos são naturalmente estressados em ambientes desconhecidos, por isso o atendimento domiciliar é ideal para felinos. A consulta inclui exame clínico completo, avaliação de pelagem, condição corporal, saúde bucal e comportamento. Verificamos calendário vacinal, orientamos sobre castração, desparasitação e enriquecimento ambiental. Oferecemos também orientações sobre nutrição felina, prevenção de doenças urinárias (muito comuns em gatos) e cuidados específicos para cada fase da vida do seu gato.','fas fa-cat','10.jpeg','gostaria de agendar consulta para meu gato',2,1),
('Consulta para Equinos','O atendimento equino é realizado diretamente na propriedade rural, haras ou cocheira onde o animal se encontra. A consulta abrange exame clínico geral, avaliação de cascos, dentição, sistema locomotor e respiratório. Realizamos vermifugação estratégica, vacinação, exames reprodutivos e orientações nutricionais específicas para cavalos de trabalho, esporte ou lazer. Também oferecemos suporte em emergências como cólicas equinas, ferimentos e avaliação pré-compra.','fas fa-horse','5.jpeg','gostaria de agendar consulta para meu equino',3,1),
('Animais Silvestres e Rurais','Atendemos animais silvestres legalizados e animais de produção rural. A consulta é feita no ambiente do animal, respeitando suas particularidades e necessidades específicas. Para silvestres, avaliamos condição geral, nutrição, enriquecimento ambiental e possíveis zoonoses. Para bovinos e outros animais de produção, realizamos exames clínicos, vacinação, manejo reprodutivo e sanitário. Cada espécie possui protocolos próprios de manuseio e contenção, garantindo segurança para o animal e a equipe.','fas fa-dove','12.jpeg','gostaria de agendar consulta para animal silvestre',4,1),
('Consultas Virtuais','Oferecemos teleconsultas veterinárias para orientação nutricional, acompanhamento pós-operatório, avaliação comportamental, segunda opinião clínica e dúvidas gerais sobre a saúde do seu animal. A consulta virtual é ideal para tutores que precisam de orientação profissional sem sair de casa. Atendemos por videochamada com horário agendado, proporcionando praticidade sem comprometer a qualidade do atendimento. Orientamos sobre dieta adequada, suplementação, manejo comportamental, cuidados com filhotes e acompanhamento de tratamentos em andamento.','fas fa-video','9.jpeg','gostaria de agendar uma consulta virtual',5,1),
('Parceria Técnica entre Clínicas','Oferecemos serviços veterinários terceirizados para clínicas e pet shops que precisam de suporte profissional especializado. Nossa parceria inclui: cirurgias gerais e ortopédicas sob demanda, atendimento ambulatorial em horários complementares, cobertura de emergências e plantões, consultoria em nutrição animal e protocolos clínicos, e apoio diagnóstico em casos complexos. Benefícios: redução de custos operacionais, acesso a profissionais capacitados sem vínculo fixo, flexibilidade de horários, qualidade garantida com registro CRMV, e ampliação do portfólio de serviços da sua clínica sem investimento em contratação.','fas fa-handshake','8.jpeg','gostaria de saber sobre parceria técnica para minha clínica',6,1);

-- Equipe
INSERT IGNORE INTO `team` (`name`,`role`,`description`,`image`,`sort_order`,`active`) VALUES
('Dra. Samla Cristie','Médica Veterinária — CRMV-GO 14064-VP','Especialista em clínica de pequenos e grandes animais, cirurgia geral e atendimento ambulatorial domiciliar.','2.jpeg',1,1),
('Capacitação Contínua','Palestras e Congressos','Participação ativa em eventos científicos, ministrando palestras sobre tratamentos inovadores e saúde animal.','9.jpeg',2,1),
('Vacinação e Medicação','Protocolos Atualizados','Seguimos protocolos rigorosos de vacinação e medicação, utilizando produtos de qualidade e técnicas seguras de aplicação.','16.jpeg',3,1),
('Atendimento Equino','Manejo e Cuidados','Atendimento especializado para equinos com foco em ortopedia, nutrição e manejo reprodutivo diretamente na propriedade.','15.jpeg',4,1);

-- Depoimentos de Clientes
INSERT IGNORE INTO `testimonials` (`name`,`initials`,`color`,`rating`,`text`,`date_label`,`sort_order`,`active`) VALUES
('Maria Fernanda S.','MF','#4285f4',5,'A Dra. Samla é simplesmente maravilhosa! Atendeu meus dois cachorros em casa, com muita paciência e carinho. Explicou tudo direitinho sobre a vacinação e os cuidados. Super recomendo!','há 2 semanas',1,1),
('João Carlos M.','JC','#ea4335',5,'Chamei a Dra. Samla para atender meus cavalos na fazenda. Profissional incrível, pontual e muito competente. Fez exames completos e orientou sobre o manejo nutricional. Nota 10!','há 1 mês',2,1),
('Ana Clara R.','AC','#fbbc05',5,'Minha gatinha Mimi ficou doente e a Dra. Samla veio atender em casa no mesmo dia. Muito atenciosa, tratou com muito carinho e a Mimi melhorou super rápido. Deus abençoe!','há 1 mês',3,1),
('Pedro Henrique L.','PH','#34a853',4.5,'Excelente profissional! Atendeu meu bezerro que estava com problemas respiratórios. Muito dedicada e conhece muito sobre animais de produção. O atendimento rural dela é diferenciado.','há 2 meses',4,1),
('Luciana B.','LB','#673ab7',5,'Melhor veterinária da região! A Dra. Samla cuidou da cirurgia do meu dog e foi impecável. O pós-operatório foi excelente e ela acompanhou cada detalhe. Muito grata!','há 3 meses',5,1),
('Renato F.','RF','#ff5722',5,'A Dra. Samla atendeu meus cavalos de vaquejada e mostrou um conhecimento excepcional em equinos. Muito profissional, atenciosa e apaixonada pelo que faz. Recomendo demais!','há 3 meses',6,1);

-- Artigos do Blog
INSERT IGNORE INTO `articles` (`title`,`slug`,`excerpt`,`content`,`image`,`category`,`author`,`read_time`,`featured`,`active`) VALUES
('Cuidados Essenciais com Cães: Guia Completo','cuidados-essenciais-caes-guia-completo',
'Seu cão precisa de atenção especial em todas as fases da vida. Saiba quais são os sinais de alerta, a importância do check-up anual, como manter a vacinação em dia e dicas de nutrição adequada para cada porte.',
'<h2>A importância do check-up anual</h2><p>Assim como os humanos, os cães precisam de consultas regulares com o veterinário. O check-up anual é fundamental para detectar precocemente doenças que podem comprometer a qualidade de vida do seu pet.</p><p>Durante a consulta de rotina, o veterinário realiza:</p><ul><li><strong>Exame físico completo</strong>: avaliação de pele, pelagem, olhos, ouvidos, boca, coração e pulmões</li><li><strong>Verificação do peso</strong>: obesidade é um problema crescente em cães domésticos</li><li><strong>Avaliação do calendário vacinal</strong>: verificar se as vacinas estão em dia</li><li><strong>Exames laboratoriais</strong>: hemograma, bioquímico e urinálise quando necessário</li></ul><h2>Vacinação: o calendário completo</h2><p>A vacinação é a principal forma de prevenir doenças graves em cães. O protocolo vacinal inicia entre 6 a 8 semanas de vida.</p><h2>Nutrição adequada para cada fase</h2><p>A alimentação do cão deve ser adaptada à sua idade, porte e nível de atividade. Evite oferecer alimentos proibidos como chocolate, cebola, alho, uvas, passas e alimentos temperados.</p>',
'11.jpeg','Cães','Dra. Samla Cristie','5 min',0,1),

('Gatos: Sinais de Doença que Você Precisa Conhecer','gatos-sinais-doenca',
'Gatos são mestres em esconder dor e desconforto. Aprenda a identificar os sinais sutis de que algo não está bem.',
'<h2>Por que gatos escondem a dor?</h2><p>Por instinto de sobrevivência, gatos escondem sinais de fraqueza. Isso pode dificultar a detecção precoce de doenças. Fique atento a mudanças sutis no comportamento do seu felino.</p><h2>Sinais de alerta</h2><ul><li><strong>Mudança de comportamento</strong>: isolamento, agressividade repentina ou letargia</li><li><strong>Alterações na alimentação</strong>: perda de apetite ou aumento excessivo</li><li><strong>Problemas urinários</strong>: dificuldade para urinar, urina com sangue</li><li><strong>Vômitos frequentes</strong>: mais de uma vez por semana é sinal de alerta</li><li><strong>Pelagem opaca</strong>: pode indicar deficiências nutricionais</li></ul><h2>Doenças comuns em gatos</h2><p>As doenças urinárias (FLUTD) são muito comuns, especialmente em gatos machos castrados. Insuficiência renal crônica também é frequente em gatos idosos. A detecção precoce pode salvar a vida do seu felino!</p>',
'17.jpeg','Gatos','Dra. Samla Cristie','4 min',0,1),

('Calendário de Vacinação para Equinos','calendario-vacinacao-equinos',
'Manter o calendário vacinal dos cavalos em dia é essencial para prevenir doenças como influenza equina, encefalomielite, tétano e raiva.',
'<h2>Vacinas essenciais para equinos</h2><p>O calendário vacinal deve ser adaptado à região, uso do animal e orientação do médico veterinário.</p><ul><li><strong>Raiva</strong>: obrigatória por lei em muitos estados. Dose única anual.</li><li><strong>Tétano</strong>: fundamental para todos os equinos. Reforço anual.</li><li><strong>Influenza Equina</strong>: especialmente para animais que participam de eventos e competições.</li><li><strong>Encefalomielite (Leste e Oeste)</strong>: doença transmitida por mosquitos, com alta mortalidade.</li><li><strong>Herpesvírus Equino</strong>: pode causar aborto em éguas e doença neurológica.</li></ul><h2>Vermifugação estratégica</h2><p>Realize exame de OPG (contagem de ovos por grama de fezes) para guiar o protocolo de vermifugação. Em geral, cavalos adultos são vermifugados a cada 3-4 meses.</p>',
'8.jpeg','Equinos','Dra. Samla Cristie','6 min',0,1),

('Quando Levar seu Animal ao Veterinário: Guia de Emergência','guia-emergencia-veterinaria',
'Saiba identificar situações de urgência e emergência veterinária e aprenda os primeiros socorros para animais.',
'<h2>Diferença entre urgência e emergência</h2><p>É importante saber diferenciar uma situação de urgência de uma emergência veterinária:</p><ul><li><strong>Urgência</strong>: situação que requer atendimento em poucas horas, mas sem risco imediato de vida. Ex: vômito isolado, ferimento superficial, claudicação leve.</li><li><strong>Emergência</strong>: risco iminente de vida que requer atendimento IMEDIATO. Ex: atropelamento, convulsões, dificuldade respiratória grave, envenenamento.</li></ul><h2>Situações que exigem atendimento imediato</h2><h3>Dificuldade respiratória</h3><p>Se seu animal está respirando com a boca aberta (especialmente gatos), fazendo ruídos ao respirar ou com as mucosas azuladas (cianose), procure ajuda IMEDIATA.</p><h3>Convulsões</h3><p>Mantenha a calma, afaste objetos que possam machucá-lo, não tente segurar a língua do animal. Registre a duração da convulsão e procure o veterinário imediatamente.</p><h3>Envenenamento</h3><p><strong>NÃO INDUZA O VÔMITO</strong> sem orientação veterinária. Substâncias perigosas comuns: raticidas, medicamentos humanos, produtos de limpeza, plantas tóxicas, chocolate, cebola, alho, uvas.</p><h2>Kit de primeiros socorros para pets</h2><ul><li>Gaze estéril e ataduras</li><li>Soro fisiológico para limpeza de ferimentos</li><li>Termômetro digital (temperatura normal: cães 38-39°C, gatos 38-39.5°C)</li><li>Luvas descartáveis</li><li>Telefone do veterinário sempre acessível</li></ul>',
'1.jpeg','Dicas Gerais','Dra. Samla Cristie','7 min',0,1);

-- FAQ
INSERT IGNORE INTO `faq` (`category`,`category_icon`,`question`,`answer`,`sort_order`,`active`) VALUES
('Cães e Gatos','fas fa-paw','Qual a frequência ideal de consultas para cães e gatos?','Para animais saudáveis, recomenda-se consultas anuais de check-up. Filhotes e animais idosos devem ser acompanhados com mais frequência — a cada 3 a 6 meses. Animais com doenças crônicas precisam de acompanhamento mais próximo, conforme orientação do veterinário.',1,1),
('Cães e Gatos','fas fa-paw','Quando devo iniciar a vacinação do meu filhote?','A vacinação de filhotes de cães geralmente inicia entre 6 a 8 semanas de vida, com a vacina V8 ou V10 (polivalente), seguida de reforços a cada 21-30 dias até completar 3 doses. Para gatos, inicia-se com a tríplice felina (V3 ou V4) na mesma idade. A vacina antirrábica é aplicada a partir de 4 meses de idade.',2,1),
('Cães e Gatos','fas fa-paw','Qual a importância da castração?','A castração previne doenças graves como tumores mamários, piometra (infecção uterina) e problemas prostáticos. Além disso, reduz comportamentos indesejados como marcação territorial, agressividade e fugas. O procedimento é seguro e recomendado a partir de 6 meses de idade.',3,1),
('Equinos','fas fa-horse','Com que frequência devo vermifugar meu cavalo?','Recomenda-se a vermifugação estratégica de equinos, idealmente com exame de OPG (contagem de ovos por grama de fezes) para guiar o protocolo. Em geral, cavalos adultos são vermifugados a cada 3-4 meses, mas animais jovens e éguas podem precisar de protocolos mais frequentes.',4,1),
('Equinos','fas fa-horse','O que é cólica equina e como prevenir?','Cólica equina é toda dor abdominal no cavalo, podendo ter diversas causas: alimentação inadequada, verminoses, torção intestinal, entre outras. É uma emergência veterinária! Para prevenir: mantenha alimentação regular e de qualidade, água limpa sempre disponível, vermifugação em dia e evite mudanças bruscas na dieta.',5,1),
('Equinos','fas fa-horse','Quais vacinas são obrigatórias para cavalos?','As vacinas essenciais para equinos incluem: raiva (obrigatória por lei), influenza equina, encefalomielite (Leste e Oeste), tétano e herpesvírus equino. O calendário vacinal deve ser adaptado à região e atividade do animal. Consulte sua veterinária para o protocolo ideal.',6,1),
('Aves e Animais Silvestres','fas fa-dove','Posso ter um animal silvestre como pet?','Sim, desde que o animal seja adquirido em criadouros ou estabelecimentos comerciais autorizados pelo IBAMA, com nota fiscal e documentação legal. É crime criar animais silvestres sem autorização. Em caso de encontrar um animal silvestre ferido, entre em contato com o IBAMA ou a polícia ambiental.',7,1),
('Aves e Animais Silvestres','fas fa-dove','Quais cuidados devo ter com aves domésticas?','Aves precisam de alimentação balanceada (ração específica + frutas e legumes), gaiola ou viveiro limpo e espaçoso, água fresca diária, exposição controlada ao sol e enriquecimento ambiental com brinquedos e poleiros variados. Consultas veterinárias periódicas são essenciais para detectar doenças respiratórias e deficiências nutricionais.',8,1),
('Emergência e Urgência','fas fa-ambulance','Meu animal ingeriu algo tóxico, o que fazer?','NÃO induza o vômito sem orientação veterinária! Alguns produtos podem causar mais danos ao voltar. Anote o que o animal ingeriu, a quantidade aproximada e o horário. Entre em contato imediatamente com um veterinário. Substâncias comuns perigosas incluem: chocolate, cebola, uvas, medicamentos humanos, produtos de limpeza e raticidas.',9,1),
('Emergência e Urgência','fas fa-ambulance','Como agir em caso de hemorragia no animal?','Em caso de sangramento intenso, aplique pressão direta sobre o ferimento com um pano limpo. Mantenha a pressão por pelo menos 5 minutos sem remover. NÃO use torniquete caseiro, pois pode piorar a situação. Mantenha o animal calmo e transporte-o imediatamente ao veterinário mais próximo.',10,1),
('Emergência e Urgência','fas fa-ambulance','Quais sinais indicam que meu animal precisa de atendimento urgente?','Procure atendimento imediato se observar: dificuldade para respirar, convulsões, vômitos ou diarreia persistentes, sangramento que não para, abdômen inchado e dolorido, incapacidade de urinar, trauma grave (atropelamento, queda), ingestão de corpo estranho ou substâncias tóxicas. Em qualquer dúvida, ligue para o veterinário!',11,1);

-- Especialidades
INSERT IGNORE INTO `specialties` (`name`,`icon`,`sort_order`,`active`) VALUES
('Cirurgia Geral','fas fa-cut',1,1),
('Cirurgia Ortopédica','fas fa-bone',2,1),
('Nutrição Animal','fas fa-apple-alt',3,1),
('Clínica Geral','fas fa-heartbeat',4,1),
('Vacinação','fas fa-syringe',5,1),
('Neonatologia','fas fa-baby',6,1),
('Dermatologia','fas fa-bug',7,1),
('Odontologia Veterinária','fas fa-teeth',8,1),
('Diagnóstico por Imagem','fas fa-x-ray',9,1),
('Emergência e Urgência','fas fa-ambulance',10,1),
('Reprodução Animal','fas fa-venus-mars',11,1),
('Medicina Preventiva','fas fa-shield-alt',12,1),
('Telemedicina Veterinária','fas fa-video',13,1),
('Terceirização para Clínicas','fas fa-handshake',14,1);

-- Clientes Parceiros
INSERT IGNORE INTO `clients` (`name`,`type`,`description`,`logo_icon`,`logo_color`,`location`,`sort_order`,`active`) VALUES
('Doma Pet','Pet Shop & Clínica','Pet shop parceiro com serviços veterinários terceirizados. Atendimento clínico, vacinação e cirurgias realizadas pela nossa equipe.','fas fa-store','#e67e22','Goiânia - GO',1,1),
('Vet Clín','Clínica Veterinária','Clínica veterinária de referência com parceria em cirurgias especializadas e cobertura de plantões noturnos e finais de semana.','fas fa-hospital','#3498db','Aparecida de Goiânia - GO',2,1),
('PetVida Centro Veterinário','Hospital Veterinário','Hospital veterinário com suporte em ortopedia e cirurgia geral, além de consultoria em nutrição animal para pacientes internados.','fas fa-clinic-medical','#2ecc71','Goiânia - GO',3,1),
('Agro Saúde Animal','Agropecuária','Agropecuária parceira para atendimento de animais de grande porte, equinos e bovinos na região metropolitana de Goiânia.','fas fa-tractor','#8B4513','Senador Canedo - GO',4,1),
('Amigo Fiel Pet Center','Pet Shop','Pet center com parceria em atendimento clínico ambulatorial, vacinação em campanha e orientação nutricional para clientes.','fas fa-paw','#9b59b6','Trindade - GO',5,1),
('CliniVet Goiás','Clínica Veterinária','Clínica parceira com foco em dermatologia e diagnóstico por imagem, com suporte cirúrgico da nossa equipe quando necessário.','fas fa-stethoscope','#e74c3c','Anápolis - GO',6,1),
('Rancho Bom Animal','Fazenda & Haras','Fazenda e haras com contrato de atendimento veterinário regular para equinos de esporte e animais de produção.','fas fa-horse','#c9a84c','Pirenópolis - GO',7,1),
('PetLove Clínica','Clínica & Estética','Clínica veterinária com parceria em procedimentos cirúrgicos, emergências e acompanhamento nutricional pós-operatório.','fas fa-heart','#e91e63','Goiânia - GO',8,1);
