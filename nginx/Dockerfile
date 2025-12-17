FROM nginx:latest

# [OKD 호환] 권한 설정 & 포트 변경
RUN chmod -R g+w /var/cache/nginx /var/run /var/log/nginx && \
    chgrp -R 0 /var/cache/nginx /var/run /var/log/nginx && \
    sed -i.bak 's/listen\(.*\)80;/listen 8080;/' /etc/nginx/conf.d/default.conf && \
    sed -i.bak 's/^user/#user/' /etc/nginx/nginx.conf

# 우리가 만든 설정 파일 덮어쓰기
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

COPY ./web /var/www/html

# 랜덤 유저(Non-Root) 지원을 위해 숫자 ID 사용
USER 1001

EXPOSE 8080
CMD ["nginx", "-g", "daemon off;"]
