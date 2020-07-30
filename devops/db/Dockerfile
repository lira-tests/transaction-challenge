FROM mysql:5.7

ARG USER_UID=1000
ARG USER_GID=1000

RUN  deluser mysql \
    && addgroup --gid $USER_GID mysql \
    && adduser --uid $USER_UID --ingroup mysql --system --no-create-home --disabled-login --disabled-password mysql
